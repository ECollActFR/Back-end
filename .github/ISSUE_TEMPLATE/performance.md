---
name: Performance Issue
about: Signaler ou améliorer les performances
title: '[PERF] '
labels: 'performance, optimization'
assignees: ''
---

<!-- 🤖 AUTOMATION: Auto-labels based on performance metrics and SLA impact -->
<!-- 🏷️ AUTO-LABELS: database, api, frontend, memory, cpu, sla-breach -->
<!-- 📊 AUTO-PROJECT: Added to performance optimization board -->

## ⚡ Problème de performance

<!-- Description claire du problème de performance constaté -->

## 📊 Type de performance

- [ ] 🐌 Temps de réponse API lent
- [ ] 💾 Utilisation excessive de mémoire
- [ ] 🗄️ Requêtes base de données lentes
- [ ] 📦 Chargement de page lent
- [ ] 🔄 Traitement batch lent
- [ ] 🌐 Problème de mise en cache
- [ ] 📈 Scalabilité limitée

## 🔍 Localisation

**Endpoint / Fonctionnalité concerné(e) :**
<!-- Ex: GET /api/users, Page de dashboard, etc. -->

**Code concerné :**
- Fichier : `src/`
- Méthode / Fonction : 

## 📉 Métriques Actuelles & SLA

**Temps de réponse :**
- Actuel : X ms / s
- Attendu : Y ms / s
- SLA défini : Z ms / s
- SLA respecté : [ ] ✅ Oui [ ] ❌ Non

**Utilisation ressources :**
- CPU : X % (seuil : Y %)
- Mémoire : X MB (seuil : Y MB)
- Requêtes DB : X queries (seuil : Y)
- Disque I/O : X MB/s (seuil : Y MB/s)
- Réseau : X Mbps (seuil : Y Mbps)

**Volume de données :**
- Nombre d'enregistrements : 
- Taille des données : 
- Croissance mensuelle : 

**Métriques métier :**
- Utilisateurs concurrents : 
- Transactions/minute : 
- Taux d'erreur : X % (SLA : Y %)
- Disponibilité : X % (SLA : Y %)

**Benchmarking :**
- Performance semaine dernière : 
- Performance mois dernier : 
- Objectif trimestriel : 
- Benchmark industrie :  

## ✅ Objectifs de Performance & SLA

**Objectifs temps de réponse :**
- API endpoints : < 200ms (P95)
- Pages web : < 2s (load complete)
- Requêtes DB : < 100ms (average)
- Traitement batch : < 30min

**Objectifs ressources :**
- CPU : < 70% (moyenne)
- Mémoire : < 80% (pic)
- Disque : < 85% utilisé
- Réseau : < 80% bande passante

**SLA définis :**
- Disponibilité : 99.9% mensuel
- Taux d'erreur : < 0.1%
- Temps de réponse P95 : < 500ms
- Recovery time : < 5min

**Objectifs scaling :**
- Utilisateurs simultanés : X
- Requêtes/second : Y
- Croissance supportée : Z%/mois
- Pic de charge : X utilisateurs

**KPIs métier :**
- Conversion rate impact : 
- User experience score : 
- Abandon rate reduction : 
- Revenue impact : 

## 🔬 Analyse / Profiling

```
# Résultats du profiling, slow query logs, etc.
```

**Outils utilisés :**
- [ ] Symfony Profiler
- [ ] Blackfire
- [ ] New Relic
- [ ] MySQL slow query log
- [ ] Chrome DevTools

## 💡 Solution proposée

<!-- Comment peux-tu améliorer les performances ? -->

**Approches possibles :**
- [ ] Optimisation des requêtes SQL
- [ ] Ajout d'index base de données
- [ ] Mise en cache (Redis, Memcached)
- [ ] Lazy loading
- [ ] Pagination
- [ ] Optimisation algorithme
- [ ] CDN pour assets statiques
- [ ] Compression
- [ ] Autre : 

## 🎯 Impact Attendu & Monitoring

**Amélioration estimée :**
- Temps de réponse : -X%
- Utilisation mémoire : -Y%
- Requêtes DB : -Z%
- CPU usage : -W%
- Coût infrastructure : -V%

**Impact SLA :**
- SLA temps de réponse : [ ] ✅ Atteint [ ] ❌ Toujours en échec
- SLA disponibilité : [ ] ✅ Amélioré [ ] ❌ Inchangé
- SLA taux d'erreur : [ ] ✅ Réduit [ ] ❌ Inchangé

**Monitoring post-optimisation :**
- [ ] 📊 Dashboard performance mis à jour
- [ ] 🚨 Alertes configurées
- [ ] 📈 Métriques surveillées (30 jours)
- [ ] 🔄 Rapports hebdomadaires
- [ ] 📝 Documentation mise à jour

**Tests de charge :**
- [ ] 🧪 Test de charge basique
- [ ] 🚀 Test de stress
- [ ] 📊 Test d'endurance
- [ ] 🔍 Test de pic de charge
- [ ] 📋 Rapport de performance

**Rollback plan :**
- [ ] 🔄 Procédure rollback définie
- [ ] ⏱️ Temps rollback : < 5min
- [ ] 📊 Monitoring rollback activé
- [ ] 👥 Équipe notifiée

**Coûts vs Bénéfices :**
- Coût développement : X heures
- Coût infrastructure mensuel : -Y €
- ROI estimé : Z mois
- Impact utilisateur : +N% satisfaction

## 🔄 Étapes pour reproduire

1. 
2. 
3. 
4. Observer la lenteur

## 🖥️ Environnement

- OS : 
- PHP : 
- Symfony : 
- Base de données : 
- Environnement : [ ] Dev [ ] Staging [ ] Production

## 📌 Tâches d'Optimisation & CI/CD

**Phase d'analyse :**
- [ ] 🔍 Analyser avec profiler (Blackfire/Xdebug)
- [ ] 📊 Identifier les goulots d'étranglement
- [ ] 📈 Établir baseline de performance
- [ ] 🎯 Définir objectifs quantifiés
- [ ] 📋 Documenter les findings

**Phase d'implémentation :**
- [ ] 🛠️ Implémenter l'optimisation
- [ ] 🧪 Tests unitaires modifiés
- [ ] 🔬 Tests de performance ajoutés
- [ ] 📝 Documentation technique
- [ ] 👥 Review code performance

**Phase de validation :**
- [ ] 📊 Mesurer l'amélioration
- [ ] 🧪 Tests de non-régression
- [ ] 🚀 Tests de charge
- [ ] 🔍 Validation en staging
- [ ] ✅ Approbation QA

**Intégration CI/CD :**
- [ ] 🤖 Tests performance automatisés
- [ ] 📊 Benchmarks dans pipeline
- [ ] 🚨 Alertes performance
- [ ] 📈 Tracking métriques
- [ ] 🔄 Déploiement progressif

**Monitoring continu :**
- [ ] 📊 Dashboard performance
- [ ] 🚨 Alertes temps réel
- [ ] 📈 Tendance monitoring
- [ ] 📋 Rapports mensuels
- [ ] 🎯 Objectifs tracking

**Documentation :**
- [ ] 📚 Guide optimisation
- [ ] 🔧 Runbook performance
- [ ] 📊 Métriques reference
- [ ] 🎓 Équipe formation

## ⚠️ Risques

<!-- Y a-t-il des risques avec l'optimisation proposée ? -->

## 🤖 Automatisation & Intégration

**GitHub Actions Integration :**
- [ ] 🏷️ Auto-labeling basé sur les métriques
- [ ] 📊 Auto-assignment équipe performance
- [ ] 🚨 Auto-escalation si SLA breach
- [ ] 📈 Auto-tracking dans project board

**Performance Monitoring Stack :**
- [ ] 📊 APM (New Relic/DataDog)
- [ ] 🔍 Infrastructure monitoring
- [ ] 📈 Business metrics tracking
- [ ] 🚨 Real user monitoring (RUM)

**Alerting Automation :**
- [ ] 🚨 Alertes seuils dépassés
- [ ] 📧 Notifications automatiques
- [ ] 📊 Rapports performance
- [ ] 🔄 Auto-scaling triggers

**Intégration Project Management :**
- [ ] 📋 Synchronisation Jira/Asana
- [ ] 📊 Dashboard performance team
- [ ] 🎯 Objectifs tracking
- [ ] 📈 KPI monitoring

**Auto-optimization :**
- [ ] 🤖 Database query optimization
- [ ] 📦 Cache warming automatique
- [ ] 🔄 Auto-scaling basé sur charge
- [ ] 📊 Performance regression detection

## 💬 Contexte Additionnel

**Screenshots profiler :**
**Graphiques performance :**
**Logs pertinents :**
**Configuration système :**
**Historique modifications :**
**Autre :**