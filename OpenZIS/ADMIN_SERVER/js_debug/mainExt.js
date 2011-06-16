//main ext builder
var main = {};
main.validUser = false;
main.ADMIN_LEVEL = 0;
var win;
var viewport;
var centerPanel;
var tabPanel;
var accordion;
var zones_tree;
var zones_root;
var dataObjects_tree;
var dataObjects_root;
var agent_panel;
var groupPermissionPanel;
var userPanel;
main.ZONES_LOADED = false;

Ext.namespace('openZis');
Ext.QuickTips.init();
Ext.form.Field.prototype.msgTarget = 'side';

Ext.onReady(function() {
	
    if(main.validUser){
        var addUserAction = new Ext.Action({
            text: 'Add User',
            handler: function(){
                user.showNewUser();
            },
            iconCls:'blist'
        });
		
        var addZoneAction = new Ext.Action({
            text: 'Add Zone',
            handler: function(){
                zones.showAddZone();
            },
            iconCls:'blist'
        });
		
        var addAgentAction = new Ext.Action({
            text: 'Add Agent',
            handler: function(){
                agent.showAddAgent();
            },
            iconCls:'blist'
        });
		
        var logoutAction = new Ext.Action({
            text: 'Logout',
            handler: function(){
                main.logout();
            },
            iconCls:'logout'
        });
		
        var expandAllZonesAction = new Ext.Action({
            text: 'Expand All',
            handler: function(){
                zones_tree.expandAll();
            },
            iconCls:'blist'
        });
		
        var closeAllZonesAction = new Ext.Action({
            text: 'Collapse All',
            handler: function(){
                zones_tree.collapseAll();
            },
            iconCls:'blist'
        });
		
        var expandAllDOAction = new Ext.Action({
            text: 'Expand All',
            handler: function(){
                dataObjects_tree.expandAll();
            },
            iconCls:'blist'
        });
		
        var closeAllDOAction = new Ext.Action({
            text: 'Collapse All',
            handler: function(){
                dataObjects_tree.collapseAll();
            },
            iconCls:'blist'
        });
		
        var addGroupPermission = new Ext.Action({
            text: 'Add Group Permission',
            handler: function(){
                groupPermission.showAddGroup();
            },
            iconCls:'blist'
        });
		
		
        var Tree = Ext.tree;
		
        var zonesLoader = new Tree.TreeLoader({
            dataUrl:'./zone/buildnavigation?lic='+main.LIC
        });
		
        zones_tree = new Tree.TreePanel({
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD:true,
            border:false,
            title:'Zones',
            tbar:[addZoneAction,expandAllZonesAction,closeAllZonesAction],
            containerScroll: true,
            loader: zonesLoader
        });
	
        zones_root = new Tree.AsyncTreeNode({
            text: 'ZIS',
            draggable:false,
            href:"javascript:zit.getZitInformation();",
            id:'zit_folder'
        });
        zones_tree.setRootNode(zones_root);
        zones_root.expand();
		
        var dataObjectLoader = new Tree.TreeLoader({
            dataUrl:'./dataobject/buildnavigation?lic='+main.LIC
        });
		
        dataObjects_tree = new Tree.TreePanel({
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD:true,
            layout:'fit',
            border:false,
            title:'Data Objects',
            tbar:[expandAllDOAction,closeAllDOAction],
            containerScroll: true,
            loader: dataObjectLoader
        });
	
        dataObjects_root = new Tree.AsyncTreeNode({
            text: 'Data Objects',
            draggable:false,
            id:'doRoot_folder'
        });
        dataObjects_tree.setRootNode(dataObjects_root);
		
        agent_panel = new Ext.Panel({
            title: 'Agents',
            layout: 'fit',
            tbar:[addAgentAction],
            border:false
        });
        agent_panel.on('expand',agent.getAgentList);
		
        groupPermissionPanel = new Ext.Panel({
            layout: 'fit',
            title: 'Group Permissions',
            tbar:[addGroupPermission],
            border:false
        });
        groupPermissionPanel.on('expand',groupPermission.getAllGroups);
		
        userPanel = new Ext.Panel({
            layout: 'fit',
            title: 'Users',
            tbar:[addUserAction],
            border:false
        });
        userPanel.on('expand',user.getAllusers);

        systemPanel = new Ext.Panel({
            layout: 'fit',
            title: 'System',
            tbar:[addUserAction],
            border:false
        });
        systemPanel.on('expand',user.getAllusers);
	
        accordion = new Ext.Panel({
            region:'west',
            id:'navigation',
            title:'Navigation',
            width: 360,
            autoWidth:false,
            collapsible: true,
            margins:'35 0 5 5',
            cmargins:'35 5 5 5',
            layout:'accordion',
            fill:false,
            tbar:[logoutAction],
            layoutConfig:{
                animate:true
            },
            items:[zones_tree, agent_panel, groupPermissionPanel, dataObjects_tree, userPanel]
        });
	
        tabPanel = new Ext.TabPanel({
            id:'tabPanel',
            region:'center',
            margins:'35 0 5 5',
            cmargins:'35 5 5 5'
        });
				
        viewport = new Ext.Viewport({
            layout:'border',
            items:
            [
            accordion,
            tabPanel
            ]
        });
	
        if(main.ADMIN_LEVEL != 1){
            accordion.remove(accordion.items.get(4));
            accordion.render();
            viewport.render();
        }
        dataObjects_root.expand();
    }
    else{

        var login = new Ext.FormPanel({
            buttonAlign:'center',
            labelWidth:80,
            url:'index/login',
            frame:true,
            title:'Please Login',
            width:230,
            defaultType:'textfield',
            monitorValid:true,
            items:[{
                fieldLabel:'Username', 
                name:'loginUsername', 
                emptyText:'Username',
                allowBlank:false 
            },{ 
                fieldLabel:'Password', 
                name:'loginPassword', 
                inputType:'password', 
                allowBlank:false 
            },{
			    xtype:'hidden',
				name:'lic'
			}],
            buttons:[{
                text:'Login',
                formBind: true,	 
                handler:function(){ 
                    login.getForm().findField('lic').setValue(main.LIC);
					login.getForm().submit({ 
                        method:'POST', 
                        waitTitle:'Connecting', 
                        waitMsg:'Sending data...',
 
                        success:function(form, action){
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            main.ADMIN_LEVEL = obj.admin.level;
                            Ext.Msg.alert('Status', 'Login Successful!', function(btn, text){
                                if (btn == 'ok'){
                                    main.loginSuccess();
                                    win.close();
                                }
                            });
                        },
 
                        failure:function(form, action){
                            if(action.failureType == 'server'){ 
                                obj = Ext.util.JSON.decode(action.response.responseText); 
                                Ext.Msg.alert('Login Failed!', obj.errors.reason); 
                            }else{ 
                                Ext.Msg.alert('Warning!', 'Authentication server is unreachable : ' + action.response.responseText); 
                            } 
                            login.getForm().reset(); 
                        } 
                    }); 
                } 
            }] 
        });
 
        win = new Ext.Window({
            layout:'fit',
            width:300,
            height:150,
            closable: false,
            resizable: false,
            plain: true,
            modal: true,
            items: [login]
        });
        win.show();
    }
});

main.logout = function(){
    Ext.Ajax.request({
        url: './index/logout',
        method:'post',
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                main.logoutSuccess();
            }
            else
            {
                Ext.Msg.alert('Error!','Logout Failed!');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Logout Failed!');
        },
        params: {}
    });
};

main.logoutSuccess = function(){
    main.validUser = false;
    window.location.reload();
};
main.loginSuccess = function(){
    main.validUser = true;
    window.location.reload();
};

main.showWindow_noBtn = function(header, html, height, width){
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        title:header,
        layout:'fit',
        width:630,
        height:400,
        closeAction:'close',
        margins:'35 0 15 5',
        cmargins:'35 5 15 5',
        plain: true,
        items: new Ext.Panel(
        {
            html: html,
            title:'',
            header:false,
            plain: true,
            border:false
        }),
        buttons: [
        {
            text: 'Close',
            handler: function(){
                win.close();
            }
        }
        ]
    });
    win.show();
};

main.createTab = function(title, actionType, action){
    var panel = new Ext.Panel({
        layout: 'fit',
        title: title
    });
    if(actionType != null){
        panel.on(actionType, function(n){
            eval(action)
            });
    }
    return panel
};

main.clearTabs = function(){
    tabPanel.removeAll(true);
};

main.addTab = function(html, title, action){
    if(action == null){
        tabPanel.add({
            title: title,
            html: html,
            closable:false
        }).show();
    }
    else{
        tabPanel.add({
            title: title,
            html: html,
            listeners: {
                activate: action
            },
            closable:false
        }).show();
    }
};

main.addTab_item = function(elem){
    tabPanel.add(elem).show();
};

main.insertLoadingPanel = function(){
    var loadingPanel = new Ext.Panel({
        title: '',
        html: '<img src="./images/loadingGif.gif" width="50px" height="50px" />Loading....',
        border:false
    });
    return loadingPanel;
};

main.yesNoConvertor = function(value){
    var result = "No";
	
    if(value == 1){
        result = "Yes";
    }
	
    return result;
};

main.urlDecode = function(str){
    str = str.replace(/%5B%5D/g, '[]');
    var length = str.length;
    for(var i=0;i<length;i++)
    {
        str = str.replace('+', ' ');
    }
    str = unescape(str);
    return str;
};
