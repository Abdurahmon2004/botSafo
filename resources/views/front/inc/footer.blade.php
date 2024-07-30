<!-- footer -->
<footer class="footer-bg footer-p" style="background: url(/front/img/bg/footer-bg.png) no-repeat;background-size: cover;">
    <div class="footer-top pt-70 pb-40">
        <div class="container">
            <div class="row justify-content-between">

                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title mb-45">
                            <img src="/front/img/logo/f_logo.png" alt="img">
                        </div>
                        <div class="footer-link">
                            Aenean pulvinar laoreet tellus ut tinct. Praesent a lectus egestas, finibus enim sit amet,
                            mollis lorem. Sed a volutpat velit. Pellet habitant morbi tristique senectus et.
                        </div>
                        <div class="footer-social  mt-30">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>

                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title">
                            <h2>Our Links</h2>
                        </div>
                        <div class="footer-link">
                            <ul>
                                <li><a href="index.html">Home</a></li>
                                <li><a href="about.html"> About Us</a></li>
                                <li><a href="services.html"> Services </a></li>
                                <li><a href="contact.html"> Contact Us</a></li>
                                <li><a href="blog.html">Blog </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title">
                            <h2>Our Services</h2>
                        </div>
                        <div class="footer-link">
                            <ul>
                                <li><a href="faq.html">FAQ</a></li>
                                <li><a href="#">Support</a></li>
                                <li><a href="#">Privercy</a></li>
                                <li><a href="#">Term & Conditions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title">
                            <h2>Our Gallery</h2>
                        </div>
                        <div class="f-insta">
                            <ul>
                                <li><a href="/front/img/instagram/f-galler-01.png" class="popup-image"><img
                                            src="img/instagram/f-galler-01.png" alt="img"></a></li>
                                <li><a href="/front/img/instagram/f-galler-02.png" class="popup-image"><img
                                            src="img/instagram/f-galler-02.png" alt="img"></a></li>
                                <li><a href="/front/img/instagram/f-galler-03.png" class="popup-image"><img
                                            src="img/instagram/f-galler-03.png" alt="img"></a></li>
                                <li><a href="/front/img/instagram/f-galler-04.png" class="popup-image"><img
                                            src="img/instagram/f-galler-04.png" alt="img"></a></li>
                                <li><a href="/front/img/instagram/f-galler-05.png" class="popup-image"><img
                                            src="img/instagram/f-galler-05.png" alt="img"></a></li>
                                <li><a href="/front/img/instagram/f-galler-06.png" class="popup-image"><img
                                            src="img/instagram/f-galler-06.png" alt="img"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class="copyright-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    Copyright © 2021 Aquato All rights reserved.
                </div>
                <div class="col-lg-6 text-right text-xl-right">
                    <ul>
                        <li><a href="#">Privercy</a></li>
                        <li><a href="#">Term &amp; Conditions</a></li>
                        <li><a href="#">Legal</a></li>

                    </ul>
                </div>

            </div>
        </div>
    </div>
</footer>
<!-- footer-end -->


<!-- JS here -->
<script src="/front/js/vendor/modernizr-3.5.0.min.js"></script>
<script src="/front/js/vendor/jquery-3.6.0.min.js"></script>
<script src="/front/js/popper.min.js"></script>
<script src="/front/js/bootstrap.min.js"></script>
<script src="/front/js/slick.min.js"></script>
<script src="/front/js/ajax-form.js"></script>
<script src="/front/js/paroller.js"></script>
<script src="/front/js/wow.min.js"></script>
<script src="/front/js/js_isotope.pkgd.min.js"></script>
<script src="/front/js/imagesloaded.min.js"></script>
<script src="/front/js/parallax.min.js"></script>
<script src="/front/js/jquery.waypoints.min.js"></script>
<script src="/front/js/jquery.counterup.min.js"></script>
<script src="/front/js/jquery.scrollUp.min.js"></script>
<script src="/front/js/jquery.meanmenu.min.js"></script>
<script src="/front/js/parallax-scroll.js"></script>
<script src="/front/js/jquery.magnific-popup.min.js"></script>
<script src="/front/js/element-in-view.js"></script>
<script src="/front/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

{{-- get contact --}}

<script>
   $('#getContact').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '{{ route('contact.get') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#getContact')[0].reset();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
</script>

{{-- get contact --}}

</body>

</html>
