/*=============== SHOW & CLOSE MENU ===============*/
const open_menu_btn = document.getElementById("open-menu");
const close_menu_btn = document.getElementById("close-menu");

open_menu_btn.addEventListener("click", () => {
    document.body.classList.toggle("show-mobile-menu");
});

close_menu_btn.addEventListener("click", () => open_menu_btn.click());

/*=============== REMOVE MOBILE MENU ===============*/
const navLinks = document.querySelectorAll(".item-link");
navLinks.forEach(link => {
    link.addEventListener("click", () => open_menu_btn.click());
});

/*=============== CHANGE HEADER STYLES ===============*/
const scroll_header = () => {
    const header = document.getElementById("header");
    if (window.scrollY >= 50) {
        header.classList.add("scroll-header");
    } else {
        header.classList.remove("scroll-header");
    }
};

/*=============== SHOW SCROLL UP ===============*/
const scroll_up = document.getElementById("scroll-up");
const show_scroll_up = () => {
    if (window.scrollY >= 450) {
        scroll_up.classList.add("show-scroll");
    } else {
        scroll_up.classList.remove("show-scroll");
    }
};

/*=============== SCROLL SECTIONS ACTIVE LINK ===============*/
const sections = document.querySelectorAll("section[id]");
const scroll_active_link = () => {
    const scroll_position = window.scrollY + 200;
    sections.forEach(current_section => {
        const section_id = current_section.getAttribute("id");
        const section_top = current_section.offsetTop - 100;
        const section_bottom = section_top + current_section.offsetHeight;
        const active_link = document.querySelector(`.nav-menu a[href="#${section_id}"]`);
        if (active_link) {
            if (scroll_position >= section_top && scroll_position <= section_bottom) {
                active_link.classList.add("active-link");
            } else {
                active_link.classList.remove("active-link");
            }
        }
    });
};

window.addEventListener("scroll", () => {
    scroll_header();
    show_scroll_up();
    scroll_active_link();
});

/*=============== SCROLL REVEAL ANIMATION ===============*/
const sr = ScrollReveal({
  distance: '60px',
  duration: 1500,
  delay: 300,
  easing: 'cubic-bezier(0.34 , 1.56 , 0.64 , 1)',
  origin: 'bottom',
  reset:true
});

sr.reveal('.home-title', { origin: 'top' });
sr.reveal('.home-btn', {  delay: 200 , origin: 'top'});
sr.reveal('.home-frying-pan', { delay:600, rotate:{z:60} });
sr.reveal('.home-rosemary-1', { delay:1200 ,origin: 'right' , rotate:{z: -60} });
sr.reveal('.home-rosemary-2', { delay:1200 ,origin: 'left', rotate:{z:-60} });
sr.reveal('.home-tomato', { delay:1200 ,origin: 'right', rotate:{z:-60} });
sr.reveal('.home-spoon', { delay:1200  });
sr.reveal('.home-onion', { delay:1200 ,origin: 'right', rotate:{z:-60} });
sr.reveal('.home-salt', { delay:1200 ,origin: 'left', distance: '120px' });
sr.reveal('.home-salt2', { delay:1200 ,origin: 'right', distance: '120px' });
sr.reveal('.home-pepper', { delay:1200 ,origin: 'top', distance: '120px' });


sr.reveal('.about-flour', { delay: 900 });
sr.reveal('.about-rossmary', { origin: 'bottom',delay: 1200 });
sr.reveal('.slide-data > *', { origin: 'top' });


sr.reveal('.header-info > *', { origin: 'top' });
sr.reveal('.card-info', { distance: '0px' , rotate:{z:-10} , origin:'left' , delay:600 });
sr.reveal('.card-dish1 , .card-dish2 , .card-dish3 , .card-dish4', { distance: '0px' , rotate:{z:-60} , scale: 0.6, duration:2000  });
sr.reveal('.card-rosemary , .card-flour1 , .card-pepper , .card-flour2 , .card-tomato , .card-flour4 , .card-flour3', { delay: 1500, distance: '0px' , rotate:{z:-60} , duration:2000  });


sr.reveal('.ingredient-1', {  distance: 0, scale: 1.7});
sr.reveal('.INGREDIENTS-images > img', { delay: 600 , distance: 0, scale: 1.7 });
sr.reveal('.INGREDIENTS-info > *', { origin: 'top' });


sr.reveal('.contact-header', { origin: 'top' });
sr.reveal('.info1', { delay: 300 ,origin: 'top' });
sr.reveal('.info2', { delay: 600 ,origin: 'top' });
sr.reveal('.info3', { delay: 900 ,origin: 'top' });
sr.reveal('.contact-gps', { delay: 700 ,origin: 'right' });


sr.reveal('.reservation-btn , .reservation-header', {  });