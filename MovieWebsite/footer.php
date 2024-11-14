<!-- Footer -->
<footer class="footer">
    <style>
        :root {
            --primary-color: #1a237e;
            --accent-color: #ff4081;
        }

        .footer {
            background: var(--primary-color);
            color: white;
            padding: 50px 0;
            margin-top: 60px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-section {
            margin: 20px;
            flex: 1;
            min-width: 250px;
        }

        .footer-section h3 {
            color: var(--accent-color);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--accent-color);
        }

        .social-links a {
            color: white;
            font-size: 1.2rem;
            transition: color 0.3s ease;
            margin-right: 15px;
        }

        .social-links a:hover {
            color: var(--accent-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .footer-section {
                flex: 100%;
                text-align: center;
            }
        }
    </style>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Về LetPhich</h3>
            <p>Nền tảng xem phim trực tuyến hàng đầu với kho phim đa dạng và chất lượng cao.</p>
        </div>
        <div class="footer-section">
            <h3>Liên kết</h3>
            <ul class="footer-links">
                <li><a href="#">Về chúng tôi</a></li>
                <li><a href="#">Điều khoản sử dụng</a></li>
                <li><a href="#">Chính sách bảo mật</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Theo dõi chúng tôi</h3>
            <div class="social-links">
                <a href="#" class="mr-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="mr-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="mr-3"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>
