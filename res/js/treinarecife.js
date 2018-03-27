/**
 * 
 */
function pickOrganization(obj){
    var orgid = $(obj).attr("_org");
	var userid = $(obj).attr("_user");
	var type = $(obj).val();
    $.ajax({
        url: '/admin/users/organization',
        type: 'post',
        data: {user_id:userid, org_id:orgid, type:type},
        dataType: 'json',
        success: function(){
            //alert('Dados atualizados com sucesso');
        },
        error: function(){
            alert('Erro ao atualizar os dados');
        }
	});
	return false;
}
