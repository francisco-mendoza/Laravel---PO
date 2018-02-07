/**
 * Created by francisco on 16-01-17.
 */
$(document).ready(function(){



    $("#btn_login").click(function(){
        $(this).attr('disabled','disabled');
        let url_login = "/auth/google";
        let hash = window.location.hash;
        if(hash != ""){
            let url_hash = hash.split('#');
            var urlParam = url_hash[1];
            url_login = "/auth/google?url="+urlParam;
        }

        $("#enter_label").remove();
        $("#icon_login").removeClass('fa fa-google fa-lg');
        $("#icon_login").addClass('fa fa-spinner fa-spin fa-2x fa-fw');
        window.location.href=url_login;
    });


});