/* http://www.webhive.com.ua */
/* author Eugene Sutula */
/* 20.01.2006 */

var grand_openingID=null;
var grand_openingRunning=false;
function stopclock(){
    if(grand_openingRunning){
        clearTimeout(grand_openingID)
        }
        grand_openingRunning=false
    }

function showtime(){
    var b="application/modules/Grandopening/externals/images/counter/";
    var f=new Date();
    var d= dest_time-f.getTime();

    if(d<=1){
        window.location.href=window.location.href
        return;
        }
        var e=Math.floor(d/1000);
    var i=Math.floor((e/3600)/24);
    var g=Math.floor(e/3600-i*24);
    var c=Math.floor((e-i*24*3600-g*3600)/60);
    var h=e-i*24*3600-g*3600-c*60;
    d=Math.floor(h/10);
    document.seconds_1.src=b+"0"+d+".png";
    document.seconds_2.src=b+"0"+(h-d*10)+".png";
    d=Math.floor(c/10);
    document.minutes_1.src=b+"0"+d+".png";
    document.minutes_2.src=b+"0"+(c-d*10)+".png";
    d=Math.floor(g/10);
    document.hours_1.src=b+"0"+d+".png";
    document.hours_2.src=b+"0"+(g-d*10)+".png";
    d=Math.floor(i/100);
    tmp1=Math.floor((i-d*100)/10);
    document.days_1.src=b+"0"+d+".png";
    document.days_2.src=b+"0"+tmp1+".png";
    document.days_3.src=b+"0"+(i-d*100-tmp1*10)+".png";
    grand_openingID=setTimeout("showtime()",1000);
    grand_openingRunning=true
}

function startclock(){
    stopclock();
    showtime()
};
		
Smoothbox.Modal.whGO_Confirm = new Class({

  Extends : Smoothbox.Modal,

  element : false,
  options : {
      title: 'Confirm?',
      description: 'Are you sure?',
      button_ok: 'Ok'
  },
  load : function()
  {
    if( this.content )
    {
      return;
    }

    this.parent();

    this.content = new Element('div', {
      id : 'TB_ajaxContent'
    });
    this.content.inject(this.window);
        
    new Element('h3', {text : en4.core.language.translate(this.options.title)}).inject(this.content);
    new Element('p', {text : en4.core.language.translate(this.options.description)}).inject(this.content);
    var buttons = new Element('div', {'class' : 'confirm_buttons'});
    new Element('button', {type:"button",
                           text:en4.core.language.translate(this.options.button_ok)}).addEvent('click', function(){
                                                                                                        this.showLoading();
                                                                                                        this.hideWindow();
                                                                                                        this.fireEvent('doAction');
                                                                                                        
                                                                                                     }.bind(this)
                                                                                    ).inject(buttons);   
    new Element('button', {type:"button",
                           text:en4.core.language.translate("Cancel")}).addEvent('click', function(){
                                                                                                            this.close();
                                                                                                            this.fireEvent('cancelAction');
                                                                                                            
                                                                                                        }.bind(this)
                                                                                    ).inject(buttons);   
    buttons.inject(this.content)
       
    this.hideLoading();
    this.showWindow();
    this.onLoad();
  },
  onCancelAction: function () {
      this.fireEvent('cancelAction', this);
  },
  onDoAction: function () {
      this.fireEvent('doAction', this);
  }

});