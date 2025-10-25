<?php include 'includes/header.php'; ?>

<main>
    <section class="page-header" style="background-image: url('https://i.pinimg.com/564x/2b/b6/c6/2bb6c638f321b01a110a17f6974e304c.jpg');">
        <div class="container">
            <h1>About Us</h1>
        </div>
    </section>

    <section class="about-info-section section-padding">
        <div class="container">
            <div class="about-info-grid">
                <div class="about-info-image">
                    <img src="assets/about.jpg" alt="Restaurant Interior">
                </div>
                <div class="about-info-content">
                    <h2 class="section-title" style="text-align: left; margin: 0 0 20px 0;">Our Story</h2>
                    <h3 style="margin-top:0;">Serving Quality Food Since 2010</h3>
                    <p>Welcome to Delicious, where we bring the best flavors to your doorstep. Our journey began with a simple passion for great food and exceptional service. We believe in using only the freshest ingredients, sourced locally whenever possible.</p>
                    <p>Our mission is to create unforgettable dining experiences, whether you're dining in with us or enjoying our food from the comfort of your home. Our vision is to be the most loved and trusted restaurant in the community.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="chefs-section section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            <h2 class="section-title">Meet Our Chefs</h2>
            <div class="chefs-grid">
                <div class="chef-card">
                    <img src="https://i.pinimg.com/564x/e9/a7/cf/e9a7cf59b2de344f6f5611b8166c342f.jpg" alt="Chef 1">
                    <h4>Chef Antonio Rossi</h4>
                    <span>Executive Chef</span>
                    <p>With 20 years of experience in Italian cuisine, Chef Antonio is the heart of our kitchen.</p>
                </div>
                <div class="chef-card">
                    <img src="https://i.pinimg.com/564x/f3/7a/12/f37a123f4b6ade86a1f868d6e98f707f.jpg" alt="Chef 2">
                    <h4>Chef Maria Lin</h4>
                    <span>Pastry Chef</span>
                    <p>Maria creates all our delightful desserts, bringing a touch of sweetness to every meal.</p>
                </div>
                <div class="chef-card">
                    <img src="https://i.pinimg.com/564x/0f/5d/c9/0f5dc95e9f80a87a7af0155b0a32194a.jpg" alt="Chef 3">
                    <h4>Chef David Chen</h4>
                    <span>Sous Chef</span>
                    <p>David ensures every dish that leaves the kitchen meets our highest standards of quality.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.page-header {
    padding: 80px 0;
    background-size: cover;
    background-position: center;
    position: relative;
    text-align: center;
}
.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}
.page-header h1 {
    position: relative;
    z-index: 2;
    color: var(--light-color);
    font-family: var(--font-heading);
    font-size: 3rem;
}
.chefs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}
.chef-card {
    text-align: center;
    background: var(--light-color);
    padding: 20px;
    border-radius: 15px;
    box-shadow: var(--shadow);
}
.chef-card img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 15px;
    border: 4px solid var(--border-color);
}
.chef-card h4 {
    font-family: var(--font-heading);
    font-size: 1.3rem;
    color: var(--dark-color);
    margin-bottom: 5px;
}
.chef-card span {
    font-size: 0.9rem;
    color: var(--primary-green);
    font-weight: 600;
    display: block;
    margin-bottom: 10px;
}
</style>

<?php include 'includes/footer.php'; ?>