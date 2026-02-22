
(function(){
  const navToggle=document.getElementById('navToggle');
  const navMenu=document.getElementById('navMenu');
  if(navToggle){navToggle.addEventListener('click',()=>{
    if(getComputedStyle(navMenu).display==='none'){navMenu.style.display='flex';navMenu.style.flexDirection='column';navMenu.style.gap='10px';navMenu.style.marginTop='10px'}else{navMenu.style.display=''}
  })}
  const yearEl=document.getElementById('year'); if(yearEl){yearEl.textContent=new Date().getFullYear();}
  const form=document.getElementById('contactForm');
  if(form){form.addEventListener('submit', function(e){
    // let backend handle; no mailto here
  })}
})();