// Zones Javascript File

var context = {};

context.getContextList = function(){
    $('zoneHtml').innerHTML = '<img src=\'./images/loadingGif.gif\' />';
    new Ajax.Request('./context/getcontextlist',
    {
        method:'post',
        parameters: {
			lic:main.LIC
		},
        onSuccess: function(transport)
        {
            try
            {
                var html = transport.responseText || "no response text";
                $('contextHtml').innerHTML = html;
            }
            catch(ex)
            {
                alert(ex);
            }
        },
        onFailure: function()
        {
            alert('Something went wrong...')
        }

    });
};

context.cancelworkArea = function(){
    $('workArea').innerHTML = "";
};

context.showAddContext = function(){
    $('workArea').innerHTML = '<table>'+
'<tr><td colspan="2" style="font-size:18px;">New Context</td></tr>'+
'<tr><td>Context Description</td><td><input type="text" id="newContextDesc" size="25" /></td></tr>'+
'<tr><td colspan="2"><input type="button" value="Save" class="button" onclick="context.createContext();" /> | '+
'<input type="button" value="Cancel" class="button" onclick="context.cancelworkArea();" /></td></tr>'+
'</table>';
};

context.showEditContext = function(contextDesc, contextId){
    $('workArea').innerHTML = '<table>'+
    '<tr><td colspan="2" style="font-size:18px;">Edit Context</td></tr>'+
    '<tr><td>Context Description</td><td><input type="text" id="editedContextDesc" size="25" value="'+contextDesc+'" /></td></tr>'+
    '<tr><td colspan="2"><input type="button" value="Save" class="button" onclick="context.updateContext(\''+contextDesc+'\')" /> | '+
    '<input type="button" value="Cancel" onclick="context.cancelworkArea();" class="button" /></td></tr>'+
    '</table><input type="hidden" id="contextId" value="'+contextId+'" />';
};

context.updateContext = function(oldContextDesc){
    var contextDesc = $('editedContextDesc').value;
    var contextId = $('contextId').value;
    if(contextDesc == '' || contextDesc == null || oldContextDesc == contextDesc){
        context.cancelworkArea();
        return;
    }
    new Ajax.Request('./context/updatecontext',
    {
        method:'post',
        parameters: {
            DESCRIPTION: contextDesc,
            ID:contextId,
			lic:main.LIC
        },
        onSuccess: function(transport)
        {
            try
            {
                var jsonData = transport.responseText || "no response text";
                var jsonObject = eval('('+jsonData+')');
                if(jsonObject.result[0].success)
                {
                    context.getContextList();
                    $('workArea').innerHTML = '';
                }
                else
                {
                    alert('There was an error please try agian');
                }
            }
            catch(ex)
            {
                alert(ex);
            }
        },
        onFailure: function()
        {
            alert('Something went wrong...')
        }

    });
};

context.createContext = function(){
    var contextDesc = $('newContextDesc').value;
    if(contextDesc == '' || contextDesc == null){
        return;
    }
    new Ajax.Request('./context/addcontext',
    {
        method:'post',
        parameters: {
            DESCRIPTION: contextDesc,
			lic:main.LIC
        },
        onSuccess: function(transport)
        {
            try
            {
                var jsonData = transport.responseText || "no response text";
                var jsonObject = eval('('+jsonData+')');
                if(jsonObject.result[0].success)
                {
                    context.getContextList();
                    $('workArea').innerHTML = '';
                }
                else
                {
                    alert('There was an error please try agian');
                }
            }
            catch(ex)
            {
                alert(ex);
            }
        },
        onFailure: function()
        {
            alert('Something went wrong...')
        }

    });
};