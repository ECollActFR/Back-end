#!/bin/bash

# Script d'export de la base de données MariaDB
# Génère un fichier SQL avec DROP TABLE, DELETE et INSERT

set -e

# Configuration
CONTAINER_NAME="neutria-backend-database"
DB_USER="userneutria"
DB_PASSWORD="RGKmnVsUVP7m3nGuLMAL3TvRdVMj6h"
DB_NAME="dbneutria"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
OUTPUT_FILE="backup_${DB_NAME}_${TIMESTAMP}.sql"

# Couleurs pour l'affichage
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Export de la base de données MariaDB${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Vérifier que le conteneur est en cours d'exécution
echo -e "${YELLOW}Vérification du conteneur...${NC}"
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo -e "${YELLOW}❌ Le conteneur ${CONTAINER_NAME} n'est pas en cours d'exécution.${NC}"
    echo -e "${YELLOW}Démarrage des conteneurs...${NC}"
    docker-compose up -d
    sleep 3
fi

echo -e "${GREEN}✓ Conteneur actif${NC}"
echo ""

# Export de la base de données
echo -e "${YELLOW}Export en cours...${NC}"
echo -e "  Base de données : ${DB_NAME}"
echo -e "  Fichier de sortie : ${OUTPUT_FILE}"
echo ""

docker exec ${CONTAINER_NAME} mariadb-dump \
  -u ${DB_USER} \
  -p${DB_PASSWORD} \
  --add-drop-table \
  --complete-insert \
  --extended-insert=FALSE \
  --skip-comments \
  --single-transaction \
  --routines \
  --triggers \
  ${DB_NAME} > "${OUTPUT_FILE}"

# Vérifier que le fichier a été créé
if [ -f "${OUTPUT_FILE}" ]; then
    FILE_SIZE=$(du -h "${OUTPUT_FILE}" | cut -f1)
    echo -e "${GREEN}✓ Export réussi !${NC}"
    echo ""
    echo -e "${BLUE}Fichier créé :${NC} ${OUTPUT_FILE}"
    echo -e "${BLUE}Taille :${NC} ${FILE_SIZE}"
    echo ""

    # Afficher un aperçu du contenu
    echo -e "${BLUE}Aperçu du fichier (10 premières lignes) :${NC}"
    head -n 10 "${OUTPUT_FILE}"
    echo ""
    echo -e "${GREEN}========================================${NC}"
    echo -e "${GREEN}  Export terminé avec succès !${NC}"
    echo -e "${GREEN}========================================${NC}"
else
    echo -e "${YELLOW}❌ Erreur lors de la création du fichier${NC}"
    exit 1
fi
