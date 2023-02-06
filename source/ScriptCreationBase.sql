#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: _competence
#------------------------------------------------------------

CREATE TABLE _competence(
        id          Int  Auto_increment  NOT NULL ,
        description Text ,
        nomCourt    Varchar (50) NOT NULL ,
        nom         Varchar (255) NOT NULL
	,CONSTRAINT _competence_AK UNIQUE (nom)
    ,CONSTRAINT _competence_AK1 UNIQUE (nomCourt)
	,CONSTRAINT _competence_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _ressource
#------------------------------------------------------------

CREATE TABLE _ressource(
        id                Int  Auto_increment  NOT NULL ,
        refQualite        Varchar (255) ,
        validationQualite Bool ,
        description       Text ,
        URL               Varchar (255) ,
        motsClefs         Text ,
        auteur            Varchar (255) ,
        publique          Bool NOT NULL ,
        obsolete          Bool NOT NULL ,
        dateAjout         Date NOT NULL ,
        dateMAJ           Date ,
        conversionTexte   Text ,
        titre             Varchar (255) NOT NULL
	,CONSTRAINT _ressource_AK UNIQUE (titre)
	,CONSTRAINT _ressource_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _typeRessource
#------------------------------------------------------------

CREATE TABLE _typeRessource(
        id          Int  Auto_increment  NOT NULL ,
        description Varchar (255) ,
        nom         Varchar (255) NOT NULL
	,CONSTRAINT _typeRessource_AK UNIQUE (nom)
	,CONSTRAINT _typeRessource_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _utilisateur
#------------------------------------------------------------

CREATE TABLE _utilisateur(
        id             Int  Auto_increment  NOT NULL ,
        nom            Varchar (255) ,
        prenom         Varchar (255) ,
        hashedPassword Varchar (255) ,
        email          Varchar (255) ,
        droits         Char (5) ,
        droitsValides  Bool ,
        jeton          Varchar (255) ,
        emailVerifie   Bool ,
        login          Varchar (255) NOT NULL
	,CONSTRAINT _utilisateur_AK UNIQUE (login)
	,CONSTRAINT _utilisateur_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _referentiel
#------------------------------------------------------------

CREATE TABLE _referentiel(
        id      Int  Auto_increment  NOT NULL ,
        niveau  Varchar (255) ,
        matiere Varchar (50) ,
        nom     Varchar (255) NOT NULL
	,CONSTRAINT _referentiel_AK UNIQUE (nom)
	,CONSTRAINT _referentiel_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _metier
#------------------------------------------------------------

CREATE TABLE _metier(
        id  Int  Auto_increment  NOT NULL ,
        nom Varchar (255) NOT NULL
	,CONSTRAINT _metier_AK UNIQUE (nom)
	,CONSTRAINT _metier_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _emailAutorise
#------------------------------------------------------------

CREATE TABLE _emailAutorise(
        id         Int  Auto_increment  NOT NULL ,
        preArobase Varchar (255) ,
        domaine    Varchar (255) NOT NULL
	,CONSTRAINT _emailAutorise_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Base
#------------------------------------------------------------

CREATE TABLE Base(
        id      Int  Auto_increment  NOT NULL ,
        version Int NOT NULL
	,CONSTRAINT Base_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _fichier
#------------------------------------------------------------
## ICI ajout contrainte unique pour id__ressource,nom...?
CREATE TABLE _fichier(
        id            Int  Auto_increment  NOT NULL ,
        type          Varchar (50) NOT NULL ,
        donnees       Longblob NOT NULL ,
        taille        Int NOT NULL ,
        nom           Varchar (100) NOT NULL ,
        id__ressource Int NOT NULL
	,CONSTRAINT _fichier_AK0 UNIQUE (nom,id__ressource)
    ,CONSTRAINT _fichier_AK1 UNIQUE (id__ressource)
	,CONSTRAINT _fichier_PK PRIMARY KEY (id)

	,CONSTRAINT _fichier__ressource_FK FOREIGN KEY (id__ressource) REFERENCES _ressource(id)

)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _requerir
#------------------------------------------------------------

CREATE TABLE _requerir(
        id             Int NOT NULL ,
        id__competence Int NOT NULL ,
        priorite       Int
	,CONSTRAINT _requerir_PK PRIMARY KEY (id,id__competence)

	,CONSTRAINT _requerir__competence_FK FOREIGN KEY (id) REFERENCES _competence(id)
	,CONSTRAINT _requerir__competence0_FK FOREIGN KEY (id__competence) REFERENCES _competence(id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _concerner
#------------------------------------------------------------

CREATE TABLE _concerner(
        id            Int NOT NULL ,
        id__ressource Int NOT NULL
	,CONSTRAINT _concerner_PK PRIMARY KEY (id,id__ressource)

	,CONSTRAINT _concerner__competence_FK FOREIGN KEY (id) REFERENCES _competence(id)
	,CONSTRAINT _concerner__ressource0_FK FOREIGN KEY (id__ressource) REFERENCES _ressource(id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _appartenir
#------------------------------------------------------------

CREATE TABLE _appartenir(
        id            Int NOT NULL ,
        id__ressource Int NOT NULL
	,CONSTRAINT _appartenir_PK PRIMARY KEY (id,id__ressource)

	,CONSTRAINT _appartenir__typeRessource_FK FOREIGN KEY (id) REFERENCES _typeRessource(id)
	,CONSTRAINT _appartenir__ressource0_FK FOREIGN KEY (id__ressource) REFERENCES _ressource(id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _decrire
#------------------------------------------------------------

CREATE TABLE _decrire(
        id              Int NOT NULL ,
        id__referentiel Int NOT NULL ,
        reference       Varchar (255)
	,CONSTRAINT _decrire_PK PRIMARY KEY (id,id__referentiel)

	,CONSTRAINT _decrire__competence_FK FOREIGN KEY (id) REFERENCES _competence(id)
	,CONSTRAINT _decrire__referentiel0_FK FOREIGN KEY (id__referentiel) REFERENCES _referentiel(id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _utiliser
#------------------------------------------------------------

CREATE TABLE _utiliser(
        id         Int NOT NULL ,
        id__metier Int NOT NULL
	,CONSTRAINT _utiliser_PK PRIMARY KEY (id,id__metier)

	,CONSTRAINT _utiliser__competence_FK FOREIGN KEY (id) REFERENCES _competence(id)
	,CONSTRAINT _utiliser__metier0_FK FOREIGN KEY (id__metier) REFERENCES _metier(id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: _publier
#------------------------------------------------------------

CREATE TABLE _publier(
        id              Int NOT NULL ,
        id__utilisateur Int NOT NULL
	,CONSTRAINT _publier_PK PRIMARY KEY (id,id__utilisateur)

	,CONSTRAINT _publier__ressource_FK FOREIGN KEY (id) REFERENCES _ressource(id)
	,CONSTRAINT _publier__utilisateur0_FK FOREIGN KEY (id__utilisateur) REFERENCES _utilisateur(id)
)ENGINE=InnoDB;

