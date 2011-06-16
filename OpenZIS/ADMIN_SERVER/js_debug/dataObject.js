// dataObject.js

var dataObject = {};

dataObject.SELECTEDGROUP = '';

dataObject.listAllVersions = function(){
    var versions = zones.versions;
    if(versions == null || versions.length == 0){
        main.clearTabs();
        main.addTab('<h2>No Versions Created</h2>','Zones');
    }
    else{
        var length = versions.length;
        var data = '[';
        for(var i = 0; i < length; i++){
			
            data += "['<a href=\"javascript:dataObject.listAllGroups("+versions[i].id+");\">"+versions[i].desc+"</a>',"+versions[i].groups;
            if(i == (length - 1)){
                data += "]";
            }
            else{
                data += "],";
            }
        }
        data += ']';
		
        var dataObj = eval(data);
		
        main.clearTabs();
        main.addTab_item(grids.createDataObjVersionsGrid(dataObj));
    }
};

dataObject.listAllGroups = function(versionId){
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './dataobject/getdataobjectgrouplist',
        method:'post',
        params: {
            VERSION:versionId,
			lic:main.LIC
        },
        success:function(response){
			myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
            dataObject.buildGroupList(obj.groups, versionId);
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Groups');
        }
    });
};

dataObject.buildGroupList = function(groups, versionId){
    if(groups == null || groups.length == 0){
        main.clearTabs();
        main.addTab('<h2>No Data Groups Created</h2>','Zones');
    }
    else{
        var length = groups.length;
        var data = '[';
        for(var i = 0; i < length; i++){
			
            data += "['<a href=\"javascript:dataObject.getAllDataObjects("+groups[i].dataObjectGroupId+");\">"+groups[i].dataObjectGroupDesc+"</a>',"+groups[i].numObjs;
            if(i == (length - 1)){
                data += "]";
            }
            else{
                data += "],";
            }
        }
        data += ']';
		
        var dataObj = eval(data);
		
        main.clearTabs();
        main.addTab_item(grids.createDataObjGroupGrid(dataObj, versionId));
    }
};

dataObject.getAllDataObjects = function(groupId){
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './dataobject/getdataobjects',
        method:'post',
        params: {
            ID:groupId,
			lic:main.LIC
        },
        success:function(response){
            myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
            dataObject.SELECTEDGROUP = groupId;
            dataObject.buildDataObjectList_HTML(obj.dataObject);
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Data Objects');
        }
    });
};

dataObject.buildDataObjectList_HTML = function(dataObjects){
    if(dataObjects == null || dataObjects.length == 0){
        main.clearTabs();
        main.addTab('<h2>No Data Objects Created</h2>','Zones');
    }
    else{
        var length = dataObjects.length;
        var data = '[';
        for(var i = 0; i < length; i++){
			
            data += "['"+dataObjects[i].dataObjectName+"',";
            if(i == (length - 1)){
                data += "]";
            }
            else{
                data += "],";
            }
        }
        data += ']';
		
        var dataObj = eval(data);
		
        main.clearTabs();
        main.addTab_item(grids.createDataObjGrid(dataObj));
    }
};