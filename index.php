<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OCP - Gestion des Interventions</title>
<style>* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height:1.6;
    color:#034d43;
    background:white;
    min-height:100vh;
}
.container { max-width:1400px; margin:0 auto; padding:0 40px; }
header {
    background:white;
    backdrop-filter: blur(8px);
    box-shadow:0 2px 15px rgba(0,0,0,0.1);
    position:fixed; width:100%; top:0; z-index:1000;
}
nav {
    display: flex;
    align-items: center;
    justify-content: space-between;  /* ligne très importante */
    padding: 2rem 40px;              /* augmente l'espace à gauche et à droite */
}

.nav-left {
  flex: 0 0 auto;
}

.nav-right {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 2rem;
}


.logo { display:flex; align-items:center; gap:10px; }
.logo img {
    width:130px; /* augmenté */
    height:70px;  /* augmenté */
}
.nav-links { display:flex; list-style:none;    margin-left: auto; /* pousse le menu à droite */
    display: flex;
    align-items: center;
    gap: 20px; align-items:center; }
.nav-links a {
    text-decoration:none; color:#034d43; font-weight:700; /* un peu plus gras */
    font-size:1.25rem; /* taille augmentée */
    padding: 0.5rem 0;   /* ajoute du confort vertical */
    position:relative;
}
.nav-links a::after {
    content:''; position:absolute; width:0; height:2px; bottom:-5px; left:0;
    background:#6ecfc5; transition:width 0.3s ease;
}
.nav-links a:hover::after { width:100%; }
.nav-links a:hover { color: #038b72; }
.login-btn {
    background: #038b72; color:white; border:none; 
    padding:14px 30px; /* padding augmenté */
    border-radius:30px; cursor:pointer; font-weight:500;
    font-size:1.2rem; /* taille augmentée */
    box-shadow:0 4px 10px rgba(0,0,0,0.2);
    transition:all 0.3s ease;
}
.login-btn:hover { transform:translateY(-2px); }

.dropdown-menu {
    position:absolute; top:100%; right:0;
    background:white; border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,0.2); min-width:180px;
    opacity:0; visibility:hidden; transform:translateY(-10px);
    transition:all 0.3s ease; z-index:1001;
}
.dropdown-menu.show {
    opacity:1; visibility:visible; transform:translateY(0);
}
.dropdown-item {
    display:block; padding:12px 20px; color:#034d43; text-decoration:none;
    border-bottom:1px solid #f0f0f0; transition:background 0.3s ease;
}
.dropdown-item:last-child { border-bottom:none; }
.dropdown-item:hover { background:#f9f9f9; color:#6ecfc5; }

main { margin-top:80px; }

.menu-toggle {
    display: none;
    font-size: 1.8rem;
    background: none;
    border: none;
    color: #034d43;
    cursor: pointer;
}

/* Hero section */
.hero {
    display:flex; flex-wrap:wrap; align-items:center;
    background:white;
    color:#026753; padding:130px 0; text-align:center;
}
.hero-content { flex:1; }
.hero h1 {
    font-size:3.4rem; margin-bottom:1rem; animation:fadeInUp 1s ease-out;
}
.hero p {
    font-size:1.4rem; margin-bottom:2rem; animation:fadeInUp 1s ease-out 0.3s both;
}
.hero-img { flex:1; text-align:center; }
.hero-img img {
    width:450px; max-width:90%; background:white; /* agrandi */
    border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.1);
    padding:10px;
    animation:fadeInUp 1s ease-out 0.6s both;
}

/* About & services */
.about, .services {
    padding:80px 0; background:white;
}

/* --- AMÉLIORATIONS POUR À PROPOS --- */
.about {
  background: #f9fdfc;
  padding: 100px 20px;
}

.about h2 {
  font-size: 2.8rem;
  margin-bottom: 3.5rem;
  color: #026753;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  text-align: center;
  font-family: 'Segoe UI Black', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.about-content {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  max-width: 1100px;
  margin: 0 auto;
  align-items: flex-start;
  justify-content: center;
}

.about-text {
  flex: 1 1 400px;
  font-size: 1.15rem;
  line-height: 1.75;
  color: #034d43;
  padding: 0 15px;
  text-align: justify;
  border-left: 4px solid #6ecfc5;
  background: #e7f2f1;
  border-radius: 10px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
}

.about-text p + p {
  margin-top: 1rem;
}

.about-features {
  flex: 1 1 400px;
  display: grid;
  grid-template-columns: 1fr;
  gap: 25px;
  padding: 0 15px;
}

.feature {
  background:white; 
  padding:1.8rem 1.5rem;
  border-radius:15px;
  box-shadow:0 10px 25px rgba(110, 207, 197, 0.15);
  transition:transform 0.3s ease, box-shadow 0.3s ease;
  cursor: default;
}

.feature:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(110, 207, 197, 0.3);
}

.feature h3 {
  color: #038b72;
  margin-bottom: 0.75rem;
  font-weight: 600;
  font-size: 1.4rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.feature p {
  font-size: 1rem;
  color: #065a4d;
  line-height: 1.6;
  font-weight: 400;
}

/* Footer */
footer {
    background:#034d43; color:white; padding:3rem 0 1rem;
}
.footer-content {
    display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:2rem; margin-bottom:2rem;
}
.footer-section h3 { margin-bottom:1rem; color:#6ecfc5; }
.footer-section p, .footer-section a { color:#cdecea; text-decoration:none; }
.footer-section a:hover { color:white; }
.footer-bottom {
    text-align:center; padding-top:1rem; border-top:1px solid #1c665c;
    color:#a7d5d1;
}

/* Animations */
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(30px); }
    to { opacity:1; transform:translateY(0); }
}

/* Responsive styles */
@media(max-width:768px){
  .menu-toggle { display: block; }

  .nav-links {
    display: none;
    flex-direction: column;
    align-items: center;
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    width: 200px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    border-radius: 10px;
    margin-top: 10px;
    z-index:1001;
  }
  .nav-links.show { display: flex; }
  .nav-links li {
    margin: 10px 0;
    width: 100%;
    text-align: center;    /* centre le texte à l’intérieur de chaque item */
    position: relative;
  }
 .nav-links li a {
  display: block;
  width: 100%;
  text-align: center;
 }

  .hero { flex-direction:column; }

  .dropdown-menu {
    position: relative; /* plus absolute */
    top: auto;
    right: auto;
    transform: none;
    opacity: 1;
    visibility: visible;
    display: none;
    box-shadow: none;
    border-radius: 0;
    width: 100%;
    max-height: 150px;      /* hauteur max en tablette */
    overflow-y: auto;       /* scroll si trop long */
  }
  .dropdown-menu.show {
    display: block;
  }

  /* Adaptation About section */
  .about-content {
    flex-direction: column;
    gap: 30px;
    padding: 0 10px;
  }
  .about-text, .about-features {
    flex: unset;
    width: 100%;
    padding: 10px;
  }
  .about-text {
    border-left: none;
    border-radius: 10px 10px 0 0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    background: #f0f9f8;
    text-align: left;
  }
  .feature {
    padding: 1.5rem 1rem;
    font-size: 0.95rem;
  }
}

/* Scrollbar moderne pour le sous-menu en mobile */
.dropdown-menu::-webkit-scrollbar {
  width: 6px;
}
.dropdown-menu::-webkit-scrollbar-thumb {
  background: #6ecfc5;
  border-radius: 3px;
}
.dropdown-menu::-webkit-scrollbar-track {
  background: #f0f0f0;
}

/* Section Services */
.services {
  background: #e0f7f5;
  padding: 80px 20px;
}

.services h2 {
  font-size: 2.8rem;
  margin-bottom: 3.5rem;
  color: #026753;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  text-align: center;
  font-family: 'Segoe UI Black', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 30px;
  max-width: 1100px;
  margin: 0 auto;
}

.service-card {
  background: white;
  padding: 2rem 1.8rem;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(110, 207, 197, 0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: default;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 40px rgba(110, 207, 197, 0.35);
}

.service-icon {
  width: 90px;
  height: 90px;
  background: #038b72;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2.5rem;
  margin-bottom: 1.3rem;
  box-shadow: 0 4px 15px rgba(3, 139, 114, 0.4);
  transition: background 0.3s ease;
}

.service-card:hover .service-icon {
  background: #06b283;
  box-shadow: 0 6px 25px rgba(6, 178, 131, 0.6);
}

.service-card h3 {
  color: #034d43;
  margin-bottom: 1rem;
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.service-card p {
  color: #065a4d;
  font-size: 1rem;
  line-height: 1.6;
  font-weight: 400;
  max-width: 280px;
}


/* Responsive tweaks */
@media(max-width: 768px) {
  .services-grid {
    grid-template-columns: 1fr;
    gap: 25px;
  }

  .service-card p {
    max-width: 100%;
  }
}


</style>

</head>
<body>
<header>
  <nav class="container">
  <div class="nav-left">
    <div class="logo">
      <img src="img/ocp.svg" alt="Logo OCP">
    </div>
  </div>

  <div class="nav-right">
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <ul class="nav-links" id="navLinks">
      <li><a href="#home">Accueil</a></li>
      <li><a href="#about">À Propos</a></li>
      <li><a href="#services">Services</a></li>
      <li><a href="#contact">Contact</a></li>
      <li class="login-dropdown">
        <button class="login-btn" onclick="toggleDropdown()">Connexion ▼</button>
        <div class="dropdown-menu" id="loginDropdown">
          <a href="login.php" class="dropdown-item">👤 Administrateur</a>
          <a href="login.php" class="dropdown-item">👨‍💼 Responsable</a>
          <a href="login.php" class="dropdown-item">🔧 Technicien</a>
          <a href="login.php" class="dropdown-item">📝 Demandeur</a>
        </div>
      </li>
    </ul>
  </div>
</nav>


</header>

<main>
    <section id="home" class="hero container">
        <div class="hero-content">
            <h1>Système de Gestion des Interventions</h1>
            <p>Optimisez la gestion de vos interventions IT avec notre plateforme moderne et intuitive</p>
        </div>
        <div class="hero-img">
            <img src="img/titre-accueil.png" alt="Image titre">
        </div>
    </section>

       <section id="about" class="about">
            <div class="container">
                <h2>À Propos de Notre Solution</h2>
                <div class="about-content">
                    <div class="about-text">
                        <p>Le service informatique du <strong>GROUPE OCP - Site KHOURIBGA</strong> a développé cette application innovante pour révolutionner la gestion des interventions IT. Notre plateforme permet une gestion optimale des demandes d'intervention, une planification efficace et un suivi en temps réel.</p>
                        <p>Conçue pour répondre aux besoins spécifiques du secteur industriel, notre solution offre une interface intuitive et des fonctionnalités avancées pour tous les niveaux d'utilisateurs.</p>
                    </div>
                    <div class="about-features">
                        <div class="feature">
                            <h3>🎯 Objectif Principal</h3>
                            <p>Optimiser la gestion des interventions IT pour une meilleure productivité et un suivi efficace des demandes.</p>
                        </div>
                        <div class="feature">
                            <h3>📊 Tableaux de Bord</h3>
                            <p>Visualisation des données sous forme de statistiques avec exports Excel et fonctions BI intégrées.</p>
                        </div>
                        <div class="feature">
                            <h3>🔧 Gestion Complète</h3>
                            <p>Suivi des interventions, inventaire des équipements et planification automatisée des tâches.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="services">
            <div class="container">
                <h2>Nos Services</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">📋</div>
                        <h3>Gestion des Interventions</h3>
                        <p>Suivi complet des demandes d'intervention avec génération automatique de fiches et statuts en temps réel.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">📅</div>
                        <h3>Planification</h3>
                        <p>Planification intelligente des interventions avec calendrier intégré et gestion des priorités.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">📊</div>
                        <h3>Statistiques & BI</h3>
                        <p>Tableaux de bord avancés avec analyses statistiques et exports vers Excel pour le reporting.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🔧</div>
                        <h3>Inventaire</h3>
                        <p>Gestion complète de l'inventaire des équipements et suivi des interventions associées.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">👥</div>
                        <h3>Gestion des Rôles</h3>
                        <p>Système de rôles adaptatif pour administrateurs, responsables, techniciens et demandeurs.</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🔔</div>
                        <h3>Notifications</h3>
                        <p>Système de notifications intelligent pour informer les utilisateurs des mises à jour importantes.</p>
                    </div>
                </div>
            </div>
        </section>
</main>
  <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>GROUPE OCP</h3>
                    <p>Leader mondial des phosphates et dérivés, l'OCP contribue à nourrir les sols pour nourrir le monde depuis 1920.</p>
                    <p><strong>Site:</strong> KHOURIBGA</p>
                    <p><strong>Service:</strong> Informatique</p>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>📧 Email: it.support@ocpgroup.ma</p>
                    <p>📞 Téléphone: +212 523 xxx xxx</p>
                    <p>📍 Adresse:  Pôle Mines Groupe OCP -  Avenue Hassan II, 25000  
    Khouribga Maroc</p>
                </div>
                <div class="footer-section">
                    <h3>Liens Utiles</h3>
                    <p><a href="#">Portail OCP</a></p>
                    <p><a href="#">Support Technique</a></p>
                    <p><a href="#">Documentation</a></p>
                    <p><a href="#">Politique de Confidentialité</a></p>
                </div>
                <div class="footer-section">
                    <h3>À Propos d'OCP</h3>
                    <p>Avec plus de 100 ans d'expérience, l'OCP Group est un acteur de référence sur le marché international des phosphates et dérivés.</p>
                    <p><strong>Encadré par:</strong> Mr AKRAM Abdeladim</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 OCP Group - Tous droits réservés. Système de Gestion des Interventions IT</p>
            </div>
        </div>
    </footer>
<script>
    function toggleDropdown() {
        document.getElementById('loginDropdown').classList.toggle('show');
    }
    document.addEventListener('click', e=>{
        const d=document.getElementById('loginDropdown');
        if(!d.contains(e.target)&&!e.target.matches('.login-btn'))d.classList.remove('show');
    });
    function toggleMenu() {
  document.getElementById('navLinks').classList.toggle('show');
}
</script>
</body>
</html>
