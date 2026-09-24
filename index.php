<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="description" content="Discover premium cuts, gourmet dishes and a culinary experience designed for true meat lovers.">
      <meta name="robots" content="index, follow">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <!--=============== FAVICON ===============-->
      <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

      <!--=============== REMIXICONS - Insert link for icons ===============-->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css">

      <!--=============== CSS ===============-->
      <link rel="stylesheet" href="assets/css/styles.css">

      <title>Gourmet Steakhouse Restaurant</title>
   </head>
   <body>
      <!--==================== HEADER & NAV ====================-->
      <header class="header" id="header">
         <nav class="nav-container">
            <a href="#home" class="nav-logo">Steakhouse</a>
            <div class="nav-menu">
               <ul class="menu-list">
                  <button class="button" id="close-menu"><i class="ri-close-line"></i></button>
                  <li class="list-item"><a href="#home" class="item-link">Home</a></li>
                  <li class="list-item"><a href="#about" class="item-link">About Us</a></li>
                  <li class="list-item"><a href="#menu" class="item-link">Menu</a></li>
                  <li class="list-item"><a href="#events" class="item-link">Events</a></li>
                  <li class="list-item"><a href="#contact" class="item-link">Contact</a></li>
                  <li class="list-item"><a href="#reservation" class="item-link">Reservation</a></li>
               </ul>
               <button class="button" id="open-menu"><i class="ri-menu-line"></i></button>

            </div>
         </nav>
      </header>

      <!--==================== MAIN ====================-->
      <main class="main">
         <!--==================== HOME SECTION ====================-->
         <section class="home section" id="home">
            <div class="home-container">
               <div class="home-data">
                  <h1 class="home-title">
                     A Premium <br>
                     And Authentic <br>
                     Steakhouse
                  </h1>
                  <a href="#reservation" class="home-btn">Book a Table</a>
               </div>
               <div class="home-images">
                  <img src="assets/img/home-salt.png" alt="" class="home-salt">
                  <img src="assets/img/home-salt.png" alt="" class="home-salt2">
                  <img src="assets/img/home-pepper.png" alt="" class="home-pepper">
                  <img src="assets/img/home-tomato.png" alt="" class="home-tomato">
                  <img src="assets/img/home-rosemary-1.png" alt="" class="home-rosemary-1">
                  <img src="assets/img/home-onion.png" alt="" class="home-onion">
                  <img src="assets/img/home-spoon.png" alt="" class="home-spoon">
                  <img src="assets/img/home-rosemary-2.png" alt="" class="home-rosemary-2">
                  <img src="assets/img/home-frying-pan.png" alt="Grilled steak and fresh vegetables" class="home-frying-pan">
               </div>
            </div>
         </section>

         <!--==================== ABOUT SECTION ====================-->
         <section class="about-section" id="about">
            <div class="about-content">
               <img src="assets/img/about-flour.png" alt="" class="about-flour">
               <div class="about-slide">
                  <img src="assets/img/about-img.png" alt="Premium meat grill" class="slide-right">
                  <div class="slide-data">
                     <h2 class="title">Discover</h2>
                     <h2 class="subtitle">Our Story</h2>
                     <p class="description">
                        Enjoy the finest experience at our steakhouse. Whether you're
                        visiting us for a romantic dinner, a business meeting, a private
                        party, or simply for a drink at the bar, our steakhouse in Lima
                        will provide you with exceptional service and an unforgettable
                        dining experience.
                     </p>
                     <a href="#menu" class="slide-btn">
                        Discover our menu
                        <i class="ri-arrow-right-long-line"></i>
                     </a>
                  </div>
                  <img src="assets/img/about-rosemary.png" alt="" class="about-rossmary">
               </div>
            </div>
         </section>

         <!--==================== MENU SECTION ====================-->
         <section class="menu-section" id="menu">
            <div class="menu-header">
               <div class="header-info">
                  <h2 class="info-title">Discover</h2>
                  <h2 class="info-subtitle">Our Menu</h2>
               </div>
               <p class="header-summary">
                  Few things compare to the pleasure of a good steak and fries, simply
                  cooked with care and attention. Rest assured that our chefs treat our
                  meat with the respect it deserves for the best quality. The open kitchen
                  in many of our steakhouses is proof of this.
               </p>
            </div>
            <!-- Menu items are loaded from the database via /api/menu/list.php -->
            <div class="menu-content" id="menu-content">
               <p class="menu-loading">Loading our menu...</p>
            </div>
         </section>

         <!--==================== EVENTS SECTION ====================-->
         <section class="about-section" id="events">
            <div class="about-content">
               <img src="assets/img/about-flour.png" alt="" class="about-flour">
               <div class="about-slide">
                  <img src="assets/img/event-img.png" alt="People enjoying a meal at a restaurant" class="slide-right">
                  <div class="slide-data">
                     <h2 class="title">Discover</h2>
                     <h2 class="subtitle">Upcoming Events</h2>
                     <p class="description">
                        Not only will you be able to taste the best
                        steak in town, but you can also meet up with
                        your old friends while enjoying the food we offer.
                     </p>
                     <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" class="slide-btn">
                        View on Facebook
                        <i class="ri-arrow-right-long-line"></i>
                     </a>
                  </div>
                  <img src="assets/img/event-spoon.png" alt="" class="about-rossmary">
               </div>
            </div>
         </section>

         <!--==================== INGREDIENTS SECTION ====================-->
         <section class="ingredients-section">
            <div class="ingredients-content">
               <div class="INGREDIENTS-info">
                  <h2 class="INGREDIENTS-title">Discover</h2>
                  <h2 class="INGREDIENTS-subtitle">The Best Ingredients</h2>
                  <p class="INGREDIENTS-description">
                     We take immense pride in carefully selecting our ingredients to ensure
                     the flavors of our food are as delicious and authentic as possible.
                     We achieve this level of excellence thanks to the attention to detail
                     we dedicate to each dish, something difficult to find in other
                     restaurants due to our superior quality.
                  </p>
               </div>
               <div class="INGREDIENTS-images">
                  <img src="assets/img/ingredient-1.png" alt="" class="ingredient-1">
                  <img src="assets/img/ingredient-2.png" alt="" class="ingredient-2">
                  <img src="assets/img/ingredient-3.png" alt="" class="ingredient-3">
                  <img src="assets/img/ingredient-4.png" alt="" class="ingredient-4">
                  <img src="assets/img/ingredient-5.png" alt="" class="ingredient-5">
                  <img src="assets/img/ingredient-6.png" alt="" class="ingredient-6">
                  <img src="assets/img/ingredient-7.png" alt="" class="ingredient-7">
                  <img src="assets/img/ingredient-8.png" alt="Natural ingredients for gourmet recipes" class="ingredient-8">
               </div>
            </div>

         </section>

         <!--==================== CONTACT SECTION ====================-->
         <section class="contact-section" id="contact">
            <div class="contact-header">
               <h2 class="contact-title">Order</h2>
               <h2 class="contact-subtitle">Contact Us</h2>
            </div>
            <div class="contact-content">
               <div class="contact-info">
                  <div class="info1">
                     <i class="ri-chat-1-line"></i>
                     <h3 class="info-title">Write to us</h3>
                     <div class="social-links">
                        <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" class="face"><i class="ri-facebook-circle-line"></i></a>
                        <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" class="face"><i class="ri-instagram-line"></i></a>
                        <a href="https://api.whatsapp.com/send?phone=51123456789&text=Hello, more information!" target="_blank" rel="noopener noreferrer" class="face"><i class="ri-whatsapp-line"></i></a>
                     </div>
                  </div>
                  <div class="info2">
                     <i class="ri-phone-line"></i>
                     <h3 class="info-title">Call us</h3>
                     <div class="Call-info">
                        <a href="tel:01154053377" class="call-text">01154053377</a>
                     </div>
                  </div>
                  <div class="info3">
                     <i class="ri-file-text-line"></i>
                     <h3 class="info-title">Reservation</h3>
                     <div class="Reservation-info">
                        <a href="tel:01154053377" class="call-text2">01154053377</a>
                     </div>
                  </div>
               </div>
               <div class="contact-gps">
                  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3407.6916846671106!2d31.380893924518723!3d29.812840975041006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145833c1ec79ea35%3A0xb95ea30246ff4a39!2z2YXYs9is2K8g2KfZhNix2LbZiNin2YY!5e1!3m2!1sar!2seg!4v1787743815788!5m2!1sar!2seg" width="600" height="450" title="Steakhouse location on Google Maps" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
               </div>
            </div>
            <!-- Contact form - saved to MySQL via /api/contact/create.php -->
            <form class="contact-form" id="contact-form" novalidate>
               <div class="contact-form-row">
                  <input type="text" name="name" placeholder="Your Name" required maxlength="100">
                  <input type="email" name="email" placeholder="Email" required maxlength="100">
               </div>
               <div class="contact-form-row">
                  <input type="tel" name="phone" placeholder="Phone (optional)" maxlength="30">
                  <input type="text" name="subject" placeholder="Subject" required maxlength="150">
               </div>
               <textarea name="message" rows="5" placeholder="Your Message" required maxlength="2000"></textarea>
               <button type="submit" class="contact-submit">Send Message <i class="ri-send-plane-line"></i></button>
               <p class="form-feedback" id="contact-feedback" role="status" aria-live="polite"></p>
            </form>
         </section>

         <!--==================== RESERVATION SECTION ====================-->
         <section class="reservation-section" id="reservation">
            <div class="reservation-content">
               <div class="bg-shadow"></div>
               <img src="assets/img/reservation-img.png" alt="" class="bg-image">
               <div class="reservation-header">
                  <h2 class="reservation-title">Reservation</h2>
                  <h2 class="reservation-subtitle">Book Your Table</h2>
               </div>
               <!-- Reservation form - saved to MySQL via /api/reservations/create.php -->
               <form class="reservation-form" id="reservation-form" novalidate>
                  <div class="form-grid">
                     <input type="text" name="customer_name" placeholder="Name" required maxlength="100">
                     <input type="tel" name="phone" placeholder="Phone" required maxlength="30">
                     <input type="email" name="email" placeholder="Email" required maxlength="100">
                     <input type="date" name="reservation_date" required>
                     <input type="time" name="reservation_time" required>
                     <input type="number" name="guests" min="1" max="20" placeholder="Number of Guests" required>
                  </div>
                  <textarea name="message" rows="3" placeholder="Message (optional)" maxlength="1000"></textarea>
                  <button type="submit" class="reservation-submit">Book a Table</button>
                  <p class="form-feedback" id="reservation-feedback" role="status" aria-live="polite"></p>
               </form>
               <div class="reservation-btn">
                  <a href="https://api.whatsapp.com/send?phone=51123456789&text=Hello, more information!" target="_blank" rel="noopener noreferrer" class="btn">or Book via WhatsApp <i class="ri-whatsapp-fill"></i></a>
               </div>
            </div>

         </section>
      </main>

      <!--==================== FOOTER ====================-->
      <footer class="footer" itemscope itemtype="https://schema.org/LocalBusiness">
         <div class="footer-content">
            <div class="footer-column">
               <h3 class="footer-title">Links</h3>
               <ul class="footer-list">
                  <li><a href="#home" class="footer-link">Home</a></li>
                  <li><a href="#menu" class="footer-link">Menu</a></li>
                  <li><a href="#contact" class="footer-link">Contact</a></li>
               </ul>
            </div>
            <div class="footer-column">
               <h3 class="footer-title">Location</h3>
               <p class="footer-address">128th Street Avenue, Miraflores,<br>Lima - Peru</p>
            </div>
            <div class="footer-column">
               <h3 class="footer-title">Working Hours</h3>
               <p class="footer-hours" itemprop="openingHours" content="Mo-Sa 09:00-20:00">Monday - Saturday: 9am - 8pm</p>
               <p class="footer-hours" itemprop="openingHours" content="Su 09:00-18:00">Sunday: 9am - 6pm</p>
            </div>
         </div>
         <div class="footer-bottom">
            <span class="footer-logo">Steakhouse</span>
            <a href="mailto:steakhouse@email.com" class="footer-email">steakhouse@email.com</a>
            <p class="footer-copy">&#169; <?php echo date('Y'); ?> All Rights Reserved By Bedimcode</p>
         </div>
      </footer>

      <!--========== SCROLL UP ==========-->
      <a href="#home" class="scroll-up" id="scroll-up"><i class="ri-arrow-up-line"></i></a>

      <!--=============== SCROLLREVEAL - Insert link for animations ===============-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/scrollReveal.js/4.0.9/scrollreveal.min.js"></script>

      <!--=============== UI / ANIMATION JS ===============-->
      <script src="assets/js/main.js"></script>

      <!--=============== API JS (fetch to PHP backend) ===============-->
      <script src="assets/js/api.js"></script>
   </body>
</html>