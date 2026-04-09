<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const currentPage = computed(() => route.meta.page ?? 'home')
</script>

<template>
  <header class="main-header">
    <div class="header-sticky">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <RouterLink class="navbar-brand" to="/">
            <img src="https://html.awaikenthemes.com/artistic/images/logo.svg" alt="Logo">
          </RouterLink>

          <div class="collapse navbar-collapse main-menu">
            <div class="nav-menu-wrapper">
              <ul class="navbar-nav mr-auto" id="menu">
                <li class="nav-item">
                  <RouterLink class="nav-link" :class="{ active: currentPage === 'home' }" to="/">Home</RouterLink>
                </li>
                <li class="nav-item">
                  <RouterLink class="nav-link" :class="{ active: currentPage === 'about' }" to="/about">About Us</RouterLink>
                </li>
                <li class="nav-item">
                  <RouterLink class="nav-link" :class="{ active: currentPage === 'services' }" to="/services">Services</RouterLink>
                </li>
                <li class="nav-item">
                  <RouterLink class="nav-link" :class="{ active: currentPage === 'contact' }" to="/contact">Contact Us</RouterLink>
                </li>
              </ul>
            </div>

            <div class="header-social-box d-inline-flex">
              <div class="header-social-links">
                <ul>
                  <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                  <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                  <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                </ul>
              </div>

              <div class="header-btn">
                <button class="btn btn-popup" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                  <img src="https://html.awaikenthemes.com/artistic/images/header-btn-dot.svg" alt="Open menu">
                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight">
                  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                  <div class="offcanvas-body">
                    <div class="header-contact-box">
                      <div class="icon-box">
                        <img src="https://html.awaikenthemes.com/artistic/images/icon-phone.svg" alt="Phone icon">
                      </div>
                      <div class="header-contact-box-content">
                        <h3>phone</h3>
                        <p>(309) 8855-314</p>
                      </div>
                    </div>

                    <div class="header-contact-box">
                      <div class="icon-box">
                        <img src="https://html.awaikenthemes.com/artistic/images/icon-mail.svg" alt="Mail icon">
                      </div>
                      <div class="header-contact-box-content">
                        <h3>email</h3>
                        <p>info@domainname.com</p>
                      </div>
                    </div>

                    <div class="header-contact-box">
                      <div class="icon-box">
                        <img src="https://html.awaikenthemes.com/artistic/images/icon-location.svg" alt="Location icon">
                      </div>
                      <div class="header-contact-box-content">
                        <h3>address</h3>
                        <p>123 Creative Lane London, SW1A 1AA United Kingdom</p>
                      </div>
                    </div>

                    <div class="header-social-links sidebar-social-links">
                      <h3>stay connected</h3>
                      <ul>
                        <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="navbar-toggle"></div>
        </div>
      </nav>
      <div class="responsive-menu"></div>
    </div>
  </header>
</template>
