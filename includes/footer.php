    </main>

    <!-- Footer -->
    <footer class="footer-custom mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold text-success">Farm-Direct</h5>
                    <p class="text-muted">Connecting farmers to your table. Fresh agricultural products delivered right to your doorstep.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="/browse.php" class="text-muted text-decoration-none">Browse Products</a></li>
                        <li><a href="/register.php" class="text-muted text-decoration-none">Become a Seller</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center text-muted">
                <p class="mb-0">&copy; <?= date('Y') ?> Farm-Direct. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2) ?>assets/js/main.js"></script>
</body>
</html>
