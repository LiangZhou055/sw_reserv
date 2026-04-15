  "use strict";
  let elem = document.documentElement;
  const sideBar = document.getElementById('sideContent');
  const mainContent = document.getElementById('mainContent');
  const menu_icon = document.getElementById('menu_icon');
  const hideBarIcon = document.getElementById('hideBarIcon');
  const searchBar = document.getElementById('searchBar');

  const side_bar_first_list = document.querySelector('.side_bar_first_list');
  const side_bar_second_list = document.querySelector('.side_bar_second_list');  
  const side_bar_third_list = document.querySelector('.side_bar_third_list');  
  const side_bar_fourth_list = document.querySelector('.side_bar_fourth_list');  
  const side_bar_fivth_list = document.querySelector('.side_bar_fivth_list');  
  const side_bar_sixth_list = document.querySelector('.side_bar_sixth_list'); 
  const side_bar_eight_list = document.querySelector('.side_bar_eight_list');
  const side_bar_eleven_list = document.querySelector('.side_bar_eleven_list');
  const side_bar_twelve_list = document.querySelector('.side_bar_twelve_list');
  const side_bar_thirty_list = document.querySelector('.side_bar_thirty_list');
  const side_bar_fourteen_list = document.querySelector('.side_bar_fourteen_list');


  const first_first_child = document.querySelector('.first_first_child');
  const first_second_child = document.querySelector('.first_second_child');
  const first_third_child = document.querySelector('.first_third_child');
  const first_fourth_child = document.querySelector('.first_fourth_child');
  const first_fivth_child = document.querySelector('.first_fivth_child');
  const first_sixth_child = document.querySelector('.first_sixth_child');
  const first_eight_child = document.querySelector('.first_eight_child');
  const first_eleven_child = document.querySelector('.first_eleven_child');
  const first_twelve_child = document.querySelector('.first_twelve_child');
  const first_thirty_child = document.querySelector('.first_thirty_child');
  const first_fourteen_child = document.querySelector('.first_fourteen_child');



  const icon1 = document.querySelector('.icon1')
  const icon2 = document.querySelector('.icon2')
  const icon3 = document.querySelector('.icon3')
  const icon4 = document.querySelector('.icon4')
  const icon5 = document.querySelector('.icon5')
  const icon6 = document.querySelector('.icon6')
  const icon8 = document.querySelector('.icon8')
  const icon11 = document.querySelector('.icon11')
  const icon12 = document.querySelector('.icon12')
  const icon13 = document.querySelector('.icon13')
  const icon14 = document.querySelector('.icon14')



  const notification_top = document.getElementsByClassName('notification_top');
  const openFullScreen = document.getElementById('openFullScreen');
  const closeFullScreen = document.getElementById('closeFullScreen');
  let social_count = document.querySelectorAll('.social_count');
  let currency = document.querySelectorAll('.currency');
  let time = 300;


  side_bar_first_list.addEventListener('click',()=>{
    first_first_child.classList.toggle('first_first_menu');
    icon1.classList.toggle('rotate180');
  })

  side_bar_second_list.addEventListener('click',()=>{
    first_second_child.classList.toggle('first_second_menu');
    icon2.classList.toggle('rotate180');
  })

  side_bar_third_list.addEventListener('click',()=>{
    first_third_child.classList.toggle('first_third_menu')
    icon3.classList.toggle('rotate180');
  })

  side_bar_fourth_list.addEventListener('click',()=>{
    first_fourth_child.classList.toggle('first_fourth_menu')
    icon4.classList.toggle('rotate180');
  })

  side_bar_fivth_list.addEventListener('click',()=>{
    first_fivth_child.classList.toggle('first_fivth_menu')
    icon5.classList.toggle('rotate180');
  })

  side_bar_sixth_list.addEventListener('click',()=>{
    first_sixth_child.classList.toggle('first_sixth_menu')
    icon6.classList.toggle('rotate180');
  })

  side_bar_eight_list.addEventListener('click',()=>{
    first_eight_child.classList.toggle('first_eight_menu')
    icon8.classList.toggle('rotate180');
  })

  side_bar_eleven_list.addEventListener('click',()=>{
    first_eleven_child.classList.toggle('first_eleven_menu')
    icon11.classList.toggle('rotate180');
  })

  side_bar_twelve_list.addEventListener('click',()=>{
    first_twelve_child.classList.toggle('first_twelve_menu')
    icon12.classList.toggle('rotate180');
  })

  side_bar_thirty_list.addEventListener('click',()=>{
    first_thirty_child.classList.toggle('first_thirty_menu')
    icon13.classList.toggle('rotate180');
  })

  side_bar_fourteen_list.addEventListener('click',()=>{
    first_fourteen_child.classList.toggle('first_fourteen_menu')
    icon14.classList.toggle('rotate180');
  })

  for (let i = 0; i < notification_top.length; i++) {
    notification_top[i].addEventListener('click',(e)=>{
          alert('This is notification')
      })
  }

  const openFull = () => {
      openFullScreen.style.display = 'none';
      closeFullScreen.style.display = 'block';
      if (elem.requestFullscreen) {
          elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
          elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
          elem.msRequestFullscreen();
        }
  }

  const closeFull = () => {
      closeFullScreen.style.display = 'none';
      openFullScreen.style.display = 'block';
      if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
  }

  const showSideBar = () => {
      sideBar.classList.toggle('marginLeft');
      mainContent.classList.toggle('added');
  }


  const complete = () => alert('Task completed successfully');
  const pending = () => alert('Task is pending now...');
  const showSearchBar = () => searchBar.style.display= 'block';
  const closeSearchBar = () => searchBar.style.display= 'none';

   
  social_count.forEach(eachSocial => {
    let updateSocial = () =>{
      let target1 = +eachSocial.getAttribute('data-target');
      let count1 = +eachSocial.innerText;
      let increment = target1/time;
      if (count1 < target1) {
        eachSocial.innerText = Math.ceil(count1 + increment);
        setTimeout(updateSocial,100)
      }else{
        eachSocial.innerText = target1
      }
    }
    updateSocial()
  })
  currency.forEach(eachCurrency => {
    let updateCurrency = () =>{
      let target = +eachCurrency.getAttribute('data-target');
      let count = +eachCurrency.innerText;
      let increment = target/time;
      if (count < target) {
        eachCurrency.innerText = Math.ceil(count + increment);
        setTimeout(updateCurrency,1)
      }else{
        eachCurrency.innerText = target
      }
    }
    updateCurrency()
  })

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
  });
 