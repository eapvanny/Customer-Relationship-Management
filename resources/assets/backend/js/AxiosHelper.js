export default class AxiosHelper {

    static  singleDownloadPdf(url, paramObject, responseType, fileName, element, callback) {
        axios.get(url, {params: paramObject,  responseType: responseType})
            .then(function(response){
                const contentType = response.headers['content-type'];
                if(contentType.includes('application/json')) {
                    response.data.text().then(text => {
                        const jsonResponse = JSON.parse(text);
                        if (!jsonResponse.status) {
                            if (typeof callback === 'function') {
                                var error = jsonResponse.message;
                                var status = jsonResponse.status;
                                callback(error,status);
                            }
                            toastr.error(jsonResponse.message);
                            $('#'+element).modal('show');
                        }
                    }).catch(error => {
                        toastr.error('Error '+error);
                    });
                } else if (contentType.includes('application/pdf')){
                    const blob = new Blob([response.data], { type: response.headers['content-type']});
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.setAttribute('download', fileName+'.pdf');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    toastr.success('Download success.');
                    $('#'+element).modal('hide');
                    $('#'+element).find('input[type="hidden"]').val(''); 
                } else {
                    toastr.error('Something when wrong!');
                    $('#'+element).modal('show');
                }
            }).catch(function(error){
                if (typeof callback === 'function') {
                    callback(error, null);
                }
            });
    }
}