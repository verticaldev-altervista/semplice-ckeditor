CKEDITOR.plugins.add('pages',   //name of our plugin
{    
    requires: ['dialog'], //requires a dialog window
    init:function(editor) {
  var b="pages";
  var c=editor.addCommand(b,new CKEDITOR.dialogCommand(b));
  c.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
  c.canUndo=true;

  //add new button to the editor
  editor.ui.addButton("pages",
  {
   label:'Add a new page',
   command:b,
   icon:this.path+"pages.png"
  });

  CKEDITOR.dialog.add(b,this.path+"ab.js") //path of our dialog file
 }
});
