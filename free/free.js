const date1 = new Date();
var years=date1.getFullYear();
var months=date1.getMonth()+1;
var days=date1.getDate();

var t = null;
t = setTimeout(time, 1000);

function time() {
    clearTimeout(t);
    var showTime = document.getElementById("refresh");
    const date = new Date();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    if(seconds==1||seconds==2||seconds==3||seconds==4||seconds==5||seconds==6||seconds==7||seconds==8||seconds==9||seconds==0){
        seconds="0"+seconds;
    }
    if(minutes==1||minutes==2||minutes==3||minutes==4||minutes==5||minutes==6||minutes==7||minutes==8||minutes==9||minutes==0){
        minutes="0"+minutes;
    }
    if(hours==1||hours==2||hours==3||hours==4||hours==5||hours==6||hours==7||hours==8||hours==9){
        hours="0"+hours;
    }
    showTime.innerHTML = years+"年"+months+"月"+days+"日"+"&ensp;"+hours+":"+minutes+":"+seconds;
    t = setTimeout(time, 1000);
}