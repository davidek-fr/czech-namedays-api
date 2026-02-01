-- Adminer 4.8.1 PostgreSQL 17.6 (Debian 17.6-0+deb13u1) dump

DROP TABLE IF EXISTS "contacts";
DROP SEQUENCE IF EXISTS contacts_id_seq;
CREATE SEQUENCE contacts_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "davidekfr001"."contacts" (
    "id" integer DEFAULT nextval('contacts_id_seq') NOT NULL,
    "full_name" character varying(100) NOT NULL,
    "email" character varying(150),
    "phone_number" character varying(20),
    "nameday_date" character(5),
    CONSTRAINT "contacts_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_contacts_date" ON "davidekfr001"."contacts" USING btree ("nameday_date");


DROP TABLE IF EXISTS "naming_calendar";
CREATE TABLE "davidekfr001"."naming_calendar" (
    "date_key" character(5) NOT NULL,
    "nameday_name" character varying(100) NOT NULL,
    CONSTRAINT "naming_calendar_pkey" PRIMARY KEY ("date_key")
) WITH (oids = false);


ALTER TABLE ONLY "davidekfr001"."contacts" ADD CONSTRAINT "contacts_nameday_date_fkey" FOREIGN KEY (nameday_date) REFERENCES naming_calendar(date_key) ON DELETE SET NULL NOT DEFERRABLE;

-- 2026-02-01 19:00:23.028066+01