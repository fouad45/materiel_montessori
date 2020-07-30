$(document).ready(function(){
    
    $('#field_staticfooter_form #name_module').live('change',function(){
        var module_id = $(this).val();
        get_hook_by_module_id(module_id);
    })
    
    function get_hook_by_module_id(module_id) {
        $.ajax({
            type: 'POST',
            url:'../modules/fieldstaticfooter/ajax.php',
            data: 'module_id='+module_id,
            dataType: 'json',
            success: function(json) {
                var obj = JSON.parse(json);
                var option = "";
                $.each(obj, function (index, value) {
                    var hook_id = value.id_hook
                    var hook_name = value.name;
                    option +="<option value='"+hook_id+"'>"+hook_name+"</option>";
                })
                if(option!=""){
                    $('#field_staticfooter_form #hook_module').html(option);
                }else {
                    option = "<option value=0>No Hook</option>";
                    $('#field_staticfooter_form #hook_module').html(option);
                }
            }
        });
    }
    
    if( $( "#field_staticfooter_form #insert_module_off" ).attr('checked')=='checked'){

        $('#field_staticfooter_form #name_module').attr('disabled','disabled');
        $('#field_staticfooter_form #hook_module').attr('disabled','disabled');
    }
            
    $( "#field_staticfooter_form input[name$='insert_module']" ).bind('click',function(){
        var insert_module = $(this).val();
        if(insert_module==0) {
            $('#field_staticfooter_form #name_module').attr('disabled','disabled');
            $('#field_staticfooter_form #hook_module').attr('disabled','disabled');
        } else {
            $('#field_staticfooter_form #name_module').removeAttr('disabled');
            $('#field_staticfooter_form #hook_module').removeAttr('disabled');
        }
    })
    
    var module_id = $('#field_staticfooter_form #name_module').val();
        get_hook_by_module_id(module_id);
    var option = "<option value=0>No Hook</option>";
   // $('#hook_module').html(option);


})