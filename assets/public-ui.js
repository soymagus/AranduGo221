(()=>{
 const topButton=document.querySelector('.back-to-top');
 const updateTopButton=()=>topButton?.classList.toggle('visible',window.scrollY>500);
 window.addEventListener('scroll',updateTopButton,{passive:true});updateTopButton();
 const menu=document.querySelector('#mainMenu'),toggle=document.querySelector('.menu-toggle');
 menu?.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>{menu.classList.remove('open');toggle?.setAttribute('aria-expanded','false')}));
 document.addEventListener('click',event=>{if(!menu?.classList.contains('open')||menu.contains(event.target)||toggle?.contains(event.target))return;menu.classList.remove('open');toggle?.setAttribute('aria-expanded','false')});
})();
