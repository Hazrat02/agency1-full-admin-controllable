<template>
  <footer class="main-footer">
    <div class="footer-work-together">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="work-together-box">
              <div class="work-together-content">
                <h3>Let's Collaborate</h3>
                <h2>Let's Work Together</h2>
              </div>

              <div class="work-together-btn">
                <RouterLink to="/contact">
                  <img src="https://html.awaikenthemes.com/artistic/images/arrow-dark.svg" alt="Arrow">
                  <span>Get in Touch</span>
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-main">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6">
            <div class="about-footer">
              <div class="footer-logo">
                <img src="https://html.awaikenthemes.com/artistic/images/footer-logo.svg" alt="Footer logo">
              </div>

              <div class="footer-contact-box">
                <div class="footer-contact-item">
                  <div class="icon-box">
                    <img src="https://html.awaikenthemes.com/artistic/images/icon-phone.svg" alt="Phone icon">
                  </div>
                  <div class="footer-contact-content">
                    <p>(309) 8855-314</p>
                  </div>
                </div>

                <div class="footer-contact-item">
                  <div class="icon-box">
                    <img src="https://html.awaikenthemes.com/artistic/images/icon-mail.svg" alt="Mail icon">
                  </div>
                  <div class="footer-contact-content">
                    <p>info@domainname.com</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-3 col-6">
            <div class="footer-links">
              <h3>quick link</h3>
              <ul>
                <li><RouterLink to="/">Home</RouterLink></li>
                <li><RouterLink to="/about">About Us</RouterLink></li>
                <li><RouterLink to="/services">Services</RouterLink></li>
                <li><RouterLink to="/contact">Contact</RouterLink></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-2 col-md-3 col-6">
            <div class="footer-links">
              <h3>support</h3>
              <ul>
                <li><RouterLink to="/contact">Contact</RouterLink></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Career</a></li>
                <li><a href="#">Help</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="footer-newsletter-form">
              <h3>Subscribe our newsletter</h3>
              <form id="newslettersForm" action="#" method="POST">
                <div class="form-group">
                  <input id="mail" type="email" name="mail" class="form-control" placeholder="Enter your email" required>
                  <button type="submit" class="btn-highlighted">subscribe</button>
                </div>
              </form>
            </div>

            <div class="footer-social-links">
              <ul>
                <li><a href="#"><i class="fa-brands fa-pinterest-p"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="footer-copyright">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <div class="footer-copyright-text">
                <p>Copyright © 2024 All Rights Reserved.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

