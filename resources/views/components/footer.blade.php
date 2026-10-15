<footer class="footer-sh">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <div class="footer-sh__brand">
                    <i class="bi bi-hexagon-fill" style="font-size: 1.2rem; margin-right: 2px;"></i>
                    SkillHive
                </div>
                <p class="footer-sh__tagline">Your skills deserve an audience. Connect with talented student freelancers.</p>
                <div class="footer-sh__social">
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                    <a href="#"><i class="bi bi-github"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6>Categories</h6>
                <ul>
                    <li><a href="{{ route('category', 'web-development') }}">Web Development</a></li>
                    <li><a href="{{ route('category', 'graphic-design') }}">Graphic Design</a></li>
                    <li><a href="{{ route('category', 'video-animation') }}">Video & Animation</a></li>
                    <li><a href="{{ route('category', 'programming') }}">Programming</a></li>
                    <li><a href="{{ route('category', 'presentation-design') }}">Presentations</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6>Company</h6>
                <ul>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('how-it-works') }}">How It Works</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6>Support</h6>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Trust & Safety</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6>For Students</h6>
                <ul>
                    <li><a href="{{ route('register') }}">Become a Seller</a></li>
                    <li><a href="{{ route('explore') }}">Browse Services</a></li>
                    <li><a href="#">Community Guidelines</a></li>
                    <li><a href="#">Success Stories</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-sh__bottom">
            <p>&copy; {{ date('Y') }} SkillHive. Built by students, for everyone.</p>
            <div class="d-flex gap-4">
                <a href="#" style="color: #6B7280; font-size: 0.8125rem;">Terms</a>
                <a href="#" style="color: #6B7280; font-size: 0.8125rem;">Privacy</a>
                <a href="#" style="color: #6B7280; font-size: 0.8125rem;">Cookies</a>
            </div>
        </div>
    </div>
</footer>
