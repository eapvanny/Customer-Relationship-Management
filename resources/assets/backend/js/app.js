/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');

window.initDeleteDialog = function () {
    $('form.myAction').submit(function (e) {
        e.preventDefault();
        var that = this;
        swal({
            title: 'Are you sure?',
            text: 'You will not be able to recover this record!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dd4848',
            cancelButtonColor: '#8f8f8f',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.value) {
                that.submit();
            }
        });
    });
};

import Account from './Account';
window.Account = Account;

import Login from './Login';
window.Login = Login;

import Site from './Site';
window.Site = Site;

import Settings from './Settings';
window.Settings = Settings;

import Generic from './Generic';
window.Generic = Generic;

import Administrator from './Administrator';
window.Administrator = Administrator;

import Academic from './Academic';
window.Academic = Academic;

import MasterData from './MasterData';
window.MasterData = MasterData;

import Curriculum from './Curriculum';
window.Curriculum = Curriculum;

import CurriculumGroup from './CurriculumGroup';
window.CurriculumGroup = CurriculumGroup;

import Student from './Student';
window.Student = Student;

import StudentCertificate  from './StudentCertificate';
window.StudentCertificate = StudentCertificate;

import AxiosHelper  from './AxiosHelper';
window.AxiosHelper = AxiosHelper;

import Reports from './reports';
window.Reports = Reports;

import HRM from './hrm';
window.HRM = HRM;

import ExamEvent from './ExamEvent';
window.ExamEvent = ExamEvent;

import ExamEventRoom from './ExamEventRoom';
window.ExamEventRoom = ExamEventRoom;


import ExamEventSubject from './ExamEventSubject';
window.ExamEventSubject = ExamEventSubject;
