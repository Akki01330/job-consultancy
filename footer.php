    </main>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <h5><i class="fas fa-briefcase"></i> <?php echo APP_NAME; ?></h5>
                    <p>Your gateway to finding the perfect job or candidate. Connect talent with opportunity.</p>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo APP_URL; ?>">Home</a></li>
                        <li><a href="<?php echo APP_URL; ?>/jobs.php">Browse Jobs</a></li>
                        <li><a href="<?php echo APP_URL; ?>/contact.php">Contact Us</a></li>
                        <li><a href="#">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <h5>For Recruiters</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo APP_URL; ?>/recruiter/register.php">Register</a></li>
                        <li><a href="<?php echo APP_URL; ?>/recruiter/login.php">Login</a></li>
                        <li><a href="#">Post a Job</a></li>
                        <li><a href="#">Browse Candidates</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <h5>For Job Seekers</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo APP_URL; ?>/jobseeker/register.php">Register</a></li>
                        <li><a href="<?php echo APP_URL; ?>/jobseeker/login.php">Login</a></li>
                        <li><a href="<?php echo APP_URL; ?>/jobs.php">Find Jobs</a></li>
                        <li><a href="#">Career Advice</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.3);">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 20px;
            margin-top: 50px;
        }
        
        footer h5 {
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        footer a:hover {
            color: white;
        }
        
        .text-md-end {
            text-align: right;
        }
        
        @media (max-width: 768px) {
            .text-md-end {
                text-align: left;
                margin-top: 15px;
            }
        }
    </style>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</body>
</html>