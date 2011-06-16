
ElementFiltering = function(){

    this.filteredElements = null;

    this.saveFilters = function(agentId, zoneId, contextId){
        Ext.Ajax.request({
            url: './dataobject/savefilters',
            method:'post',
            params: {
                agent_id:agentId,
                zone_id:zoneId,
                context_id:contextId,
                filtered_elements:this.filteredElements,
				lic:main.LIC
            },
            success:function(response){
                obj = Ext.util.JSON.decode(response.responseText);
                if(obj.success)
                {
                    Ext.MessageBox.alert('Success', "Filters Saved");
                }
                else
                {
                    Ext.Msg.alert('Error!','Error Saving Filters');
                }
            },
            failure:function(response){
                Ext.Msg.alert('Error!','Error Saving Filters');
            }
        });
    };

    this.buildElementFilterTree = function(agentId, zoneId, contextId){
        var treeLoader = new Ext.tree.TreeLoader({
            dataUrl: './dataobject/getfilterabledataobjects',
            baseParams:{
                AGENT_ID: agentId,
                ZONE_ID: zoneId,
                CONTEXT_ID: contextId,
                lic: main.LIC
            }
        });

        var tree = new Ext.tree.TreePanel({
            title: 'Element Filtering',
            height: 300,
            width: 400,
            useArrows:true,
            autoScroll:true,
            animate:true,
            enableDD:true,
            containerScroll: true,
            rootVisible: false,
            layout:'fit',
            frame: false,
            root: new Ext.tree.AsyncTreeNode({
                text: 'Data Objects',
                draggable:false,
                id:'data_objects_node'
            }),
            loader: treeLoader,
            listeners: {
                'checkchange': function(node, checked){
                    if(node.hasChildNodes()){
                        node.eachChild(function(child){
                            child.ui.toggleCheck(checked);
                            if(checked){
                                child.disable();
                            }
                            else{
                                child.enable();
                            }
                            child.fireEvent('checkchange', child, checked);
                        });
                    }
                    if(checked){
                        node.getUI().addClass('complete');
                    }else{
                        node.getUI().removeClass('complete');
                    }
                },
                'insert': function(tree, self){
                    alert('here');
                    if(self.getUI().isChecked()){
                        if(self.hasChildNodes()){
                            self.eachChild(function(child){
                                child.ui.toggleCheck(true);
                                child.fireEvent('checkchange', child, true);
                            });
                        }
                    }
                }
            },
            buttonAlign:'left',
            buttons: [{
                text: 'Save Filters',
                handler: function(){
                    var checkedNodes = tree.getChecked();
                    openZis.elementFiltering.filteredElements = '';
                    Ext.each(checkedNodes, function(node){
                        if(openZis.elementFiltering.filteredElements.length > 0){
                            openZis.elementFiltering.filteredElements += '|';
                        }
                        openZis.elementFiltering.filteredElements += node.id;
                    });
                    openZis.elementFiltering.saveFilters(agentId, zoneId, contextId);
                }
            }]
        });
        tree.getRootNode().expand(true, false);
        return tree;
    };
};

openZis.elementFiltering = new ElementFiltering();


