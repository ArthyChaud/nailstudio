DROP TABLE IF EXISTS RDV;
DROP TABLE IF EXISTS TYPE_SERVICE;

INSERT INTO TYPE_SERVICE(libelle) VALUES
	('typeUn'),
	('typeDeux');

INSERT INTO RDV(date_rdv,heure,type_service_id) VALUES
	('2020-12-29','08:00',1),
	('2020-12-29','08:30',1),
	('2020-12-29','10:00',2),
	('2020-12-29','13:00',1),
	('2020-12-29','14:30',2);
