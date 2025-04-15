/** *******************************************
 *  Скрип для календаря записи на прием
 *
 **********************************************/

function refreshIframe()
{
    setInterval(refreshIframeOnce, timeOut);  // timeOut опеределен в resources/views/olga_calendar/iframe.blade.php
}

async function refreshIframeOnce() {
    if(pause){ // pause опеределен в resources/views/olga_calendar/iframe.blade.php
        return;
    }
    let response = await fetch(url); // url опеределен в resources/views/olga_calendar/iframe.blade.php

    if (response.ok) {

        let json = await response.json();

        let delta = parseInt(json.delta);

        if(delta < timeOut/1000){
            let href = self.location.href;
            href = href.replace(/\?.+/,'');
            href += '?r=' + Math.random(); //Чтобы пробить кэш браузера
            self.location.href = href;
        }
    } else {
        alert("Ошибка HTTP: " + response.status);
    }
}



function onloadCalendar()
{
    const activeButtons = document.getElementsByClassName('opened');

    for(let i=0; i<activeButtons.length; i++){  // Привязываем активным кнопкам обработчик событий
        activeButtons[i].addEventListener('click', function(e){ onclickCalendar(this)});
    }

    const closeButtons = document.getElementsByClassName('close-button');
    for(let i=0; i<closeButtons.length; i++){  // Привязываем активным кнопкам обработчик событий
        closeButtons[i].addEventListener('click', function(e){ this.parentNode.style.display = 'none'; pause=false;});
    }

    refreshIframe();


}




function onclickCalendar(oneDate)
{
  pause = true; // Перестаем обновлять календарь. pause опеределен в resources/views/olga_calendar/iframe.blade.php

  setBlock(oneDate.dataset.date);

  const form = document.getElementById('form1');
  form.style.display = 'block';

  const form1date1 = document.getElementById('form1date1');
  let d = new Date(oneDate.dataset.date);
  form1date1.innerText = d.toLocaleDateString('ru-RU', {day:"numeric",  month: "long"});

  clearAppointments();

  const appointments = JSON.parse(oneDate.dataset.appointments);

  let hoursBusy = [];

  let divHour;

  for(let i=0; i<appointments.length; i++){

      // TODO работает, но нужен рефакторинг

      let appointment = appointments[i];

      let hourStart = parseInt(appointment.hourStart);
      let hourFinish = parseInt(appointment.hourFinish);

      hoursBusy.push(hourStart);

      divHour = document.getElementById('hour'+hourStart );

      let point = document.createElement('div');
      point.className = 'point busy';
      point = divHour.appendChild(point);

      let dStart = new Date(appointment.start);
      let minuteStart = dStart.getMinutes();

      point.style.left = minuteStart+'px';
      point.style.width = Math.min(60-minuteStart, appointment.minutes )+'px';

      while( ++hourStart < hourFinish){
          divHour = document.getElementById('hour'+hourStart );
          point = document.createElement('div');
          point.className = 'point busy';
          divHour.appendChild(point);
          hoursBusy.push(hourStart);
      }

      if( hourStart === hourFinish){
          divHour = document.getElementById('hour'+hourStart );
          point = document.createElement('div');
          point.className = 'point busy';
          point = divHour.appendChild(point);

          let dFinish = new Date(appointment.finish);
          let minuteFinish = dFinish.getMinutes();

          point.style.width = minuteFinish +'px';
          hoursBusy.push(hourStart);
      }

  }

  for(let i=10; i<18; i++){
      if(hoursBusy.find((element) => element === i) === undefined ){
          divHour = document.getElementById('hour'+i);
          let point = document.createElement('div');

          point.className = 'point free';
          point = divHour.appendChild(point);

          point.addEventListener("click", function(){
              openForm2(i,oneDate.dataset.date);
          })
      }
  }
}

async function setBlock(start) {
    // blockUrl, _token опеределены в resources/views/olga_calendar/iframe.blade.php
    await fetch(blockUrl, {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            "_token": _token,
            "start": start
        }),
    });
}

function clearAppointments()
{
    const grid = document.getElementsByClassName('point');
    for(let i=0; i<grid.length; i++){
        grid[i].remove();
    }
}

function openForm2(hourStart, dateAt)
{
    const form1 = document.getElementById('form1');
    const form2 = document.getElementById('form2');

    document.getElementById('form1date2').innerText = document.getElementById('form1date1').innerText
    document.getElementById('times').innerText = hourStart+':00 - '+(hourStart+1)+':00';

    const f2 = document.getElementById('form2form');

    f2.elements['start'].value = hourStart+':00:00';
    f2.elements['finish'].value = hourStart+':59:00';
    f2.elements['date_at'].value = dateAt;

    form1.style.display = 'none';
    form2.style.display = 'block';
}
