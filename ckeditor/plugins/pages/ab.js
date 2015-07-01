CKEDITOR.dialog.add("pages",function( editor ){

	return {
  title:'Select page',
  resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
  minWidth:300,
  minHeight:100,
  onShow:function(){ 
  },
  onLoad:function(){ 
    dialog = this; 
    this.setupContent();
  },
  onOk:function(){
  },
  contents:[
  {  id:"info",
    name:'info',
    label:'Tab',
    elements:[

     {
      id : 'page',
      type : 'text',
      label : 'new page',
      accessKey : 'P',
      items :
      [
      'new page'
      ]
     },
     {  
      type:'html',
      html:'<span style="">'+'Select the page name'+'</span>'
     }
    ]
  }
  ],
  buttons:[{
   type:'button',
   id:'okBtn',
   label: 'Set',
   onClick: function(){
      addCode(); //function for adding time to the source
   }
  }, CKEDITOR.dialog.cancelButton],
};

 function addCode(){
		//var dialog = this;	
		//get the value of 'format' field in the 'info' tab of the dialog box
		var t = this.dialog.getValueOf('info', 'page');
				   
		editor.insertHtml("<a href=\'index.php?page="+t+"\' >"+t+"</a>");
		this.dialog.hide();
 };

});
