---
name: Bug Report
about: Signaler un bug ou un problème
title: '[BUG] '
labels: 'bug, needs-triage'
assignees: ''
---

<!-- 🤖 AUTOMATION: GitHub Actions will auto-label based on severity and environment -->
<!-- 🏷️ AUTO-LABELS: critical, high, medium, low, production, staging, development -->
<!-- 📊 AUTO-PROJECT: Will be added to appropriate project board based on impact -->

## 🐛 Description du bug

<!-- Une description claire et concise du problème -->

## 🔴 Sévérité & Impact Métier

**Sévérité technique :**
- [ ] 🔥 Critique (application inutilisable, perte de données)
- [ ] 🔴 Haute (fonctionnalité majeure cassée)
- [ ] 🟠 Moyenne (fonctionnalité mineure cassée)
- [ ] 🟡 Basse (problème cosmétique, workaround possible)

**Impact métier :**
- [ ] 💰 Impact financier direct (perte de revenus, coûts supplémentaires)
- [ ] 👥 Impact utilisateur majeur (perte d'utilisateurs, insatisfaction)
- [ ] ⚖️ Impact réglementaire/conformité
- [ ] 📈 Impact sur les KPIs métier
- [ ] 🔒 Impact sécurité
- [ ] 📊 Impact reporting/analytics

**Urgence :**
- [ ] 🚨 Intervention immédiate (< 4h)
- [ ] ⏰ Intervention prioritaire (< 24h)
- [ ] 📅 Intervention planifiée (< 72h)
- [ ] 🕐 Intervention normale (> 72h)

## 📋 Étapes pour reproduire

1. Aller sur '...'
2. Cliquer sur '...'
3. Faire défiler jusqu'à '...'
4. Observer l'erreur

## ✅ Comportement attendu

<!-- Description claire de ce qui devrait se passer normalement -->

## ❌ Comportement actuel

<!-- Description de ce qui se passe réellement -->

## 📸 Captures d'écran

<!-- Si applicable, ajouter des captures d'écran pour illustrer le problème -->

## 🔧 Environnement

**Backend :**
- OS : [ex: Ubuntu 22.04, macOS 14.0, Windows 11]
- PHP : [ex: 8.2.0]
- Symfony : [ex: 7.3.0]
- Base de données : [ex: MariaDB 10.11.2]
- Docker : [ex: 24.0.0]

**Frontend (si applicable) :**
- Navigateur : [ex: Chrome 120, Firefox 115, Safari 17]
- Appareil : [ex: Desktop, iPhone 15, iPad]
- Résolution : [ex: 1920x1080]

**Environnement :**
- [ ] Développement (local)
- [ ] Staging
- [ ] Production

## 📊 Logs et messages d'erreur

```
Coller ici les logs, stack traces ou messages d'erreur pertinents
```

**Fichiers de logs concernés :**
- `var/log/dev.log`
- `var/log/prod.log`
- Console navigateur

## 🔍 Informations supplémentaires

### Requête API (si applicable)

**Endpoint :**
```http
POST /api/endpoint
Authorization: Bearer token...
Content-Type: application/json

{
  "data": "example"
}
```

**Réponse :**
```json
{
  "error": "Message d'erreur",
  "code": 500
}
```

### Code concerné (si identifié)

**Fichier :** `src/Controller/ExampleController.php`
**Ligne :** 45

```php
// Code problématique
```

## 🔄 Fréquence de reproduction

- [ ] Se produit à chaque fois (100%)
- [ ] Se produit souvent (>50%)
- [ ] Se produit parfois (<50%)
- [ ] Se produit rarement
- [ ] S'est produit une seule fois

## 🤖 Triage Automatique

**Catégorie automatique :**
- [ ] API/Backend (Symfony, Doctrine, API Platform)
- [ ] Base de données (MySQL, migrations)
- [ ] Authentification/Sécurité (JWT, voters)
- [ ] Performance (temps de réponse, mémoire)
- [ ] Configuration/Déploiement (Docker, CI/CD)
- [ ] Frontend/UX (si applicable)
- [ ] Documentation/Tests

**Composants affectés :**
- [ ] Entity/Repository
- [ ] Controller/API Resource
- [ ] Security/Voter
- [ ] Service/Processor
- [ ] Migration/Schema
- [ ] Configuration
- [ ] Tests
- [ ] Documentation

**Intégration CI/CD :**
- [ ] Bloque les déploiements en production
- [ ] Bloque les déploiements en staging
- [ ] Tests CI/CD en échec
- [ ] Pipeline affecté : [ ] build [ ] test [ ] deploy [ ] security

**Assignation automatique suggérée :**
- Backend Lead : @username
- DevOps : @username
- QA : @username

## 🛠️ Solutions de contournement

<!-- Y a-t-il un moyen temporaire de contourner ce problème ? -->

## 🔗 Issues liées

<!-- Lien vers d'autres issues similaires ou connexes -->
<!-- - #123 -->
<!-- - #456 -->

## 📌 Tâches de résolution & Checklist Revue

**Tâches techniques :**
- [ ] Identifier la cause racine
- [ ] Écrire un test qui reproduit le bug
- [ ] Corriger le bug
- [ ] Vérifier que les tests passent
- [ ] Tester manuellement
- [ ] Ajouter des tests de non-régression
- [ ] Mettre à jour la documentation si nécessaire
- [ ] Déployer le correctif

**Checklist revue code :**
- [ ] Code respecte les conventions du projet
- [ ] Tests unitaires ajoutés (>80% couverture)
- [ ] Tests fonctionnels ajoutés
- [ ] Documentation API mise à jour
- [ ] Changelog mis à jour
- [ ] Performance vérifiée (pas de régression)
- [ ] Sécurité vérifiée
- [ ] Compatibilité backward vérifiée

**Vérification CI/CD :**
- [ ] Pipeline build passe
- [ ] Pipeline test passe
- [ ] Pipeline security scan passe
- [ ] Pipeline deploy staging réussi
- [ ] Tests E2E en staging passent

**Communication :**
- [ ] Notifier les équipes impactées
- [ ] Mettre à jour le statut du service
- [ ] Communiquer aux utilisateurs (si nécessaire)
- [ ] Documenter dans le runbook

## 💡 Contexte additionnel

**Historique :**
- Date de première apparition :
- Évolution du problème :
- Déploiements récents liés :

**Impact utilisateur :**
- Nombre d'utilisateurs affectés :
- Retours utilisateurs :
- Tickets support liés :

**Données affectées :**
- Type de données :
- Volume de données :
- Backup disponible :

**Monitoring & Alertes :**
- Alertes déclenchées :
- Métriques impactées :
- Dashboards concernés :

**Autre :**
<!-- Toute autre information utile -->