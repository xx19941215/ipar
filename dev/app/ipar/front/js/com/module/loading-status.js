define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.simple-template-engine'
], function(z, s, net, tpl) {
    'use strict';

    var action,value;
    var status_set = {};

    //     0           0           0
    // is_send   is_responsed  has_result

    function loadingStatusSet(action,value){
        status_set[action] = value;
    }

    function isSend() {
        var action, status;
        for (action in status_set) {
            status = status_set[action];
            if (status[0] != 1) {
                return false;
            }
        }
        return true;
    }

    function isCompleted() {
        var action, status;
        for (action in status_set) {
            status = status_set[action];
            if (status[1] === 0) {
                return false;
            }
        }
        return true;
    }

    function hasResults() {
        var action, status;
        for (action in status_set) {
            status = status_set[action];
            if (status[2] === 1) {
                return true;
            }
        }
        return false;
    }

    function checkAllStatusIsLoading(){
        if (isSend() && !isCompleted()){
            return 0; // all responsed ok
        } else {
            return 1; // loading
        }
    }

    function checkOneStatusIsLoading(){
        if (Object.keys(status_set).length == 1){
            if (isSend() && !isCompleted()){
                return 1; // loading
            } else {
                return 0;
            }
        }
    }

    function checkAllResultStatusIsNull(){
        if (Object.keys(status_set).length == 1){
            if (s('.main').hasClass('has-data')){
                return 0;
            } else {
                s('.main').addClass('has-data');
                return 1;
            }
        }

        // if(Object.keys(status_set).length == 4)
        if (isCompleted() && !hasResults()){
            // if (s('.main').hasClass('has-data')){
            //     return 0;
            // }
            return 1; //all result status is 0
        } else {
            // s('.main').addClass('has-data');
            return 0;
        }
    }

    return {
        checkAllResultStatusIsNull: checkAllResultStatusIsNull,
        loadingStatusSet: loadingStatusSet,
        checkAllStatusIsLoading: checkAllStatusIsLoading,
        checkOneStatusIsLoading: checkOneStatusIsLoading
    };
});
