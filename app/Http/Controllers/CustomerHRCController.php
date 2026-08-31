<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\CustomerHRC;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class CustomerHRCController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view customer_hrc', [
            'only' => ['index']
        ]);

        $this->middleware('type.permission:create customer_hrc', [
            'only' => ['create', 'store']
        ]);

        $this->middleware('type.permission:update customer_hrc', [
            'only' => ['edit', 'update']
        ]);

        $this->middleware('type.permission:delete customer_hrc', [
            'only' => ['destroy']
        ]);

        // Restrict Customer HRC to Super Admin OR Manager with NSM position
        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            if (
                $user->role_id !== AppHelper::USER_SUPER_ADMIN &&
                !(
                    $user->role_id === AppHelper::USER_MANAGER &&
                    $user->position === 'NSM'
                ) &&
                $user->type !== AppHelper::HRC
            ) {
                abort(403, 'You do not have permission to access Customer HRC.');
            }

            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $loggedInUser = auth()->user();

        $query = CustomerHRC::with('user');

        $is_filter = false;

        /*
        |--------------------------------------------------------------------------
        | Customer HRC visibility
        |--------------------------------------------------------------------------
        */

        $isManagement = in_array($loggedInUser->role_id, [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMINISTRATOR,
            AppHelper::USER_DIRECTOR,
        ]) || (
            $loggedInUser->role_id === AppHelper::USER_MANAGER &&
            $loggedInUser->position === 'NSM'
        );

        if (!$isManagement) {
            // Normal HRC user can only see customers created by himself
            $query->where('user_id', $loggedInUser->id);
        }

        if ($isManagement) { // Management / NSM can select all HRC employees
            $employees = User::where('type', AppHelper::HRC)
                ->get(['id', 'username', 'family_name', 'name'])
                ->mapWithKeys(function ($user) {
                    return [$user->id => $user->username . ' (' . $user->full_name . ')'];
                })
                ->toArray();
        } else { // Normal employee can only select himself
            $employees = User::where('id', $loggedInUser->id)
                ->where('type', AppHelper::HRC)->get(['id', 'username', 'family_name', 'name'])
                ->mapWithKeys(function ($user) {
                    return [$user->id => $user->username . ' (' . $user->full_name . ')'];
                })
                ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | Date filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled(['date1', 'date2'])) {
            $is_filter = true;

            $startDate = Carbon::parse($request->date1)->startOfDay();
            $endDate = Carbon::parse($request->date2)->endOfDay();

            $query->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | User filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {
            $is_filter = true;

            $selectedUserId = $request->user_id;

            $query->where('user_id', $selectedUserId);
        }

        /*
        |--------------------------------------------------------------------------
        | DataTables
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {

            $customersHRC = $query->orderBy('id', 'desc');

            return DataTables::of($customersHRC)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {

                    if ($search = $request->input('search.value')) {

                        $query->where(function ($q) use ($search) {

                            $q->where('name', 'LIKE', "%{$search}%")

                                ->orWhereHas('user', function ($userQuery) use ($search) {

                                    $userQuery->where(function ($q) use ($search) {

                                        $q->where('family_name', 'LIKE', "%{$search}%")
                                            ->orWhere('name', 'LIKE', "%{$search}%")
                                            ->orWhere('family_name_latin', 'LIKE', "%{$search}%")
                                            ->orWhere('name_latin', 'LIKE', "%{$search}%")

                                            ->orWhereRaw(
                                                "CONCAT(family_name, ' ', name) LIKE ?",
                                                ["%{$search}%"]
                                            )

                                            ->orWhereRaw(
                                                "CONCAT(family_name_latin, ' ', name_latin) LIKE ?",
                                                ["%{$search}%"]
                                            );
                                    });
                                });
                        });
                    }
                })

                ->addColumn('created_by', function ($customer) {

                    if (!$customer->user) {
                        return 'N/A';
                    }

                    return $customer->user->user_lang === 'en'
                        ? ($customer->user->full_name_latin ?? 'N/A')
                        : ($customer->user->full_name ?? 'N/A');
                })

                ->addColumn('area', fn($customer) => $customer->area)

                ->addColumn(
                    'customer_code',
                    fn($customer) => $customer->code
                )

                ->addColumn(
                    'customer_name',
                    fn($customer) => $customer->name
                )

                ->addColumn(
                    'customer_type',
                    fn($customer) =>
                    AppHelper::CUSTOMER_TYPE_HRC[$customer->customer_type] ?? 'N/A'
                )

                ->addColumn(
                    'phone',
                    fn($customer) => $customer->phone
                )

                ->addColumn('action', function ($customer) {

                    $btn = '<div class="change-action-item">';

                    $canEdit = in_array(auth()->user()->role_id, [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMINISTRATOR,
                        AppHelper::USER_DIRECTOR,
                        AppHelper::USER_MANAGER,
                        AppHelper::USER_RSM,
                        AppHelper::USER_ASM,
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ]);

                    if ($canEdit) {
                        $btn .= '<a title="Edit"
                            href="' . route('customerhrc.edit', $customer->id) . '"
                            class="btn btn-primary btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>';
                    }

                    if (auth()->user()->can('delete customer')) {
                        $btn .= '<a href="' . route('customerhrc.destroy', $customer->id) . '"
                            class="btn btn-danger btn-sm delete"
                            title="Delete">
                            <i class="fa fa-fw fa-trash"></i>
                        </a>';
                    }

                    if (!$canEdit && !auth()->user()->can('delete customer')) {
                        $btn .= '<span style="font-weight:bold; color:red;">
                            No Action
                        </span>';
                    }

                    return $btn . '</div>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view(
            'backend.customer-hrc.list',
            compact('employees', 'is_filter')
        );
    }
    public function create()
    {
        $customerHRC = null;
        $customerHRCType = AppHelper::CUSTOMER_TYPE_HRC;
        $user = auth()->user();
        return view('backend.customer-hrc.add', compact('customerHRC', 'customerHRCType'));
    }

    public function store(Request $request)
    {

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:customers,phone',
            'area' => 'required|string|max:255',
            'customer_type' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            // File upload
            'outlet_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg,gif|max:10000|dimensions:min_width=50,min_height=50',
            // Base64 image
            'outlet_photo_base64' => 'nullable|string',
        ];

        // Make outlet_photo required if neither file nor base64 is provided
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64')) {
            $rules['outlet_photo'] = 'required|image|mimes:jpg,jpeg,png,webp,svg,gif|max:10000|dimensions:min_width=50,min_height=50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userType = auth()->user()->type;
        // Determine prefix based on user type
        switch ($userType) {
            case AppHelper::HRC:
                $prefix = 'CPV';
                break;
            case AppHelper::SALE:
                $prefix = 'CPP';
                break;
            case AppHelper::SE:
                $prefix = 'CPV';
                break;
            default:
                $prefix = 'CUS'; // fallback/default prefix
                break;
        }
        // Generate unique customer code
        $lastCustomer = CustomerHRC::orderBy('id', 'desc')->first();
        $lastCodeNumber = $lastCustomer && $lastCustomer->code ? (int) substr($lastCustomer->code, 4) : 0;
        $newCodeNumber = $lastCodeNumber + 1;
        $code = $prefix . '-' . str_pad($newCodeNumber, 5, '0', STR_PAD_LEFT);

        // Prepare data for storage
        $data = [
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'customer_type' => $request->customer_type,
            'user_type' => auth()->user()->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            'code' => $code,
        ];

        // Handle outlet photo file upload if exists
        if ($request->hasFile('outlet_photo')) {
            try {
                $file = $request->file('outlet_photo');
                $fileName = 'outlet_' . time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
                $filePath = 'Uploads/' . $fileName;

                $resizedImage = AppHelper::resizeAndCompressImage($file);

                Storage::disk('public')->put($filePath, $resizedImage);

                $data['outlet_photo'] = $filePath;
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to process image: ' . $e->getMessage())->withInput();
            }
        }
        // Handle outlet base64 image if provided and no file is uploaded
        elseif ($request->filled('outlet_photo_base64')) {
            try {
                // Resize and compress base64 image using AppHelper
                $resizedImage = AppHelper::resizeAndCompressBase64Image($request->outlet_photo_base64);

                $fileName = 'Uploads/outlet_' . time() . '_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put($fileName, $resizedImage);

                $data['outlet_photo'] = $fileName;
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to process image: ' . $e->getMessage())->withInput();
            }
        }

        try {
            CustomerHRC::create($data);

            if ($request->has('saveandcontinue')) {
                return redirect()->route('customerhrc.create')->with('success', 'Customer HRC created successfully.');
            }
            return redirect()->route('customerhrc.index')->with('success', 'Customer HRC created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create customer: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $customerHRC = CustomerHRC::findOrfail($id);
        $customerHRCType = AppHelper::CUSTOMER_TYPE_HRC;
        return view('backend.customer-hrc.add', compact('customerHRC', 'customerHRCType'));
    }


    public function update(Request $request, $id)
    {
        $customerHRC = CustomerHRC::findOrfail($id);
        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customer_hrc', 'phone')
                    ->ignore($customerHRC->id, 'id'),
            ],
            'area' => 'required|string|max:255',
            'customer_type' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'outlet_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg,gif|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
        ];

        // Make outlet_photo required if neither file nor base64 is provided and no existing photo exists
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64') && !$customerHRC->outlet_photo) {
            $rules['outlet_photo'] = 'required|image|mimes:jpg,jpeg,png,webp,svg,gif|max:10000|dimensions:min_width=50,min_height=50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare data for update
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'area' => $request->area,
            'customer_type' => $request->customer_type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
        ];

        // Generate code if none exists
        if (!$customerHRC->code) {
            $userType = auth()->user()->type;
            // Determine prefix based on user type
            switch ($userType) {
                case AppHelper::HRC:
                    $prefix = 'CPV';
                    break;
                case AppHelper::SALE:
                    $prefix = 'CPP';
                    break;
                case AppHelper::SE:
                    $prefix = 'CPV';
                    break;
                default:
                    $prefix = 'CUS'; // fallback/default prefix
                    break;
            }
            // Generate unique customer code
            $lastCustomer = CustomerHRC::orderBy('id', 'desc')->first();
            $lastCodeNumber = $lastCustomer && $lastCustomer->code ? (int) substr($lastCustomer->code, 4) : 0;
            $newCodeNumber = $lastCodeNumber + 1;
            $data['code'] = $prefix . '-' . str_pad($newCodeNumber, 5, '0', STR_PAD_LEFT);
        }

        // Handle outlet photo file upload if exists
        if ($request->hasFile('outlet_photo')) {
            try {
                // Delete existing photo if it exists
                if ($customerHRC->outlet_photo && Storage::disk('public')->exists($customerHRC->outlet_photo)) {
                    Storage::disk('public')->delete($customerHRC->outlet_photo);
                }

                $file = $request->file('outlet_photo');
                $fileName = 'outlet_' . time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
                $filePath = 'Uploads/' . $fileName;

                // Resize and compress the image using AppHelper
                $resizedImage = AppHelper::resizeAndCompressImage($file);

                // Optional: Check image size after compression
                // $sizeInKB = AppHelper::getImageSizeInKB($resizedImage);
                // if ($sizeInKB > 500) {
                //     // If still too large, resize to smaller dimensions
                //     $resizedImage = AppHelper::resizeToSpecificSize($file, 800, 800, 60);
                // }

                Storage::disk('public')->put($filePath, $resizedImage);
                $data['outlet_photo'] = $filePath;
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to process image: ' . $e->getMessage())->withInput();
            }
        }
        // Handle outlet base64 image if provided and no file is uploaded
        elseif ($request->filled('outlet_photo_base64')) {
            try {
                // Delete existing photo if it exists
                if ($customerHRC->outlet_photo && Storage::disk('public')->exists($customerHRC->outlet_photo)) {
                    Storage::disk('public')->delete($customerHRC->outlet_photo);
                }

                // Resize and compress base64 image using AppHelper
                $resizedImage = AppHelper::resizeAndCompressBase64Image($request->outlet_photo_base64);

                $fileName = 'Uploads/outlet_' . time() . '_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put($fileName, $resizedImage);
                $data['outlet_photo'] = $fileName;
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to process image: ' . $e->getMessage())->withInput();
            }
        }
        // Clear outlet_photo if existing photo is deleted and no new photo is provided
        elseif ($request->has('delete_outlet_photo') && $customerHRC->outlet_photo) {
            if (Storage::disk('public')->exists($customerHRC->outlet_photo)) {
                Storage::disk('public')->delete($customerHRC->outlet_photo);
            }
            $data['outlet_photo'] = null;
        }

        try {
            $customerHRC->update($data);
            return redirect()->route('customerhrc.index')->with('success', 'Customer HRC updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update customer HRC: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $customerHRC = CustomerHRC::find($id);
        if ($customerHRC) {
            $customerHRC->delete();
            return redirect()->back()->with('success', "customer HRC has been deleted!");
        }
        return redirect()->back()->with('error', "customer HRC not found!");
    }
}
