from django.db import models
from django.core.exceptions import ValidationError

class Cittadino(models.Model):
    cssn = models.CharField("CSSN", max_length=16, primary_key=True, db_column='CSSN')
    nome = models.CharField("Nome", max_length=50, db_column='Nome', null=True, blank=True)
    cognome = models.CharField("Cognome", max_length=50, db_column='Cognome', null=True, blank=True)
    indirizzo = models.CharField("Indirizzo", max_length=100, db_column='Indirizzo', null=True, blank=True)
    numero_civico = models.IntegerField("Numero Civico", db_column='NumeroCivico', null=True, blank=True)
    luogo_nascita = models.CharField("Luogo di Nascita", max_length=100, db_column='LuogoNascita', null=True, blank=True)
    data_nascita = models.DateField("Data di Nascita", db_column='DataNascita', null=True, blank=True)

    class Meta:
        db_table = 'Cittadini'
        verbose_name = "Cittadino"
        verbose_name_plural = "Cittadini"
        ordering = ['cognome', 'nome']

    def __str__(self):
        return f"{self.nome} {self.cognome} ({self.cssn})"

class Ospedale(models.Model):
    id_ospedale = models.AutoField(primary_key=True, db_column='IDOspedale')
    nome_ospedale = models.CharField("Nome Ospedale", max_length=100, db_column='NomeOspedale', null=True, blank=True)
    indirizzo = models.CharField("Indirizzo", max_length=100, db_column='Indirizzo', null=True, blank=True)
    numero_civico = models.IntegerField("Numero Civico", db_column='NumeroCivico', null=True, blank=True)
    citta = models.CharField("Città", max_length=100, db_column='Citta', null=True, blank=True)
    numero_telefono = models.CharField("Numero Telefono", max_length=20, db_column='NumeroTelefono', null=True, blank=True)
    codice_sanitario_direttore = models.OneToOneField(
        Cittadino,
        on_delete=models.PROTECT, # PROTECT is equivalent to SQL's RESTRICT
        db_column='CodiceSanitarioDirettore',
        unique=True,
        verbose_name="Direttore Sanitario",
        null=True, blank=True
    )

    class Meta:
        db_table = 'Ospedali'
        verbose_name = "Ospedale"
        verbose_name_plural = "Ospedali"
        ordering = ['nome_ospedale']

    def __str__(self):
        return self.nome_ospedale

class Patologia(models.Model):
    id_patologia = models.AutoField(primary_key=True, db_column='IDPatologia')
    nome_patologia = models.CharField("Nome Patologia", max_length=200, db_column='NomePatologia', null=True, blank=True)
    tipologia = models.CharField("Tipologia", max_length=50, db_column='Tipologia', null=True, blank=True)

    class Meta:
        db_table = 'Patologie'
        verbose_name = "Patologia"
        verbose_name_plural = "Patologie"
        ordering = ['nome_patologia']

    def __str__(self):
        return self.nome_patologia

class Ricovero(models.Model):
    id_ricovero = models.AutoField(primary_key=True, db_column='IDRicovero')
    cittadino = models.ForeignKey(
        Cittadino,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='CSSNCittadino',
        verbose_name="Cittadino"
    )
    ospedale = models.ForeignKey(
        Ospedale,
        on_delete=models.CASCADE, # Deleting an Ospedale will delete its Ricoveri
        db_column='IDOspedale',
        verbose_name="Ospedale"
    )
    data_ricovero = models.DateField("Data Ricovero", db_column='DataRicovero', null=True, blank=True)
    durata_ricovero = models.IntegerField("Durata Ricovero (giorni)", db_column='DurataRicovero', null=True, blank=True)
    costo_ricovero = models.DecimalField("Costo Ricovero (€)", max_digits=10, decimal_places=2, db_column='CostoRicovero', null=True, blank=True)
    motivo_ricovero = models.CharField("Motivo Ricovero", max_length=255, db_column='MotivoRicovero', null=True, blank=True)
    patologie = models.ManyToManyField(
        Patologia,
        through='RicoveroPatologie',
        related_name='ricoveri',
        verbose_name="Patologie"
    )

    class Meta:
        db_table = 'Ricoveri'
        verbose_name = "Ricovero"
        verbose_name_plural = "Ricoveri"
        ordering = ['-data_ricovero']

    def __str__(self):
        return f"Ricovero {self.id_ricovero} del {self.data_ricovero}"

class RicoveroPatologie(models.Model):
    # This is the explicit "through" model for the ManyToMany relationship
    ricovero = models.ForeignKey(Ricovero, on_delete=models.CASCADE, db_column='IDRicovero')
    patologia = models.ForeignKey(Patologia, on_delete=models.CASCADE, db_column='IDPatologia')

    class Meta:
        db_table = 'Ricovero_Patologie'
        # Set a composite primary key to mimic the original database
        unique_together = (('ricovero', 'patologia'),)
        verbose_name = "Relazione Ricovero-Patologia"
        verbose_name_plural = "Relazioni Ricovero-Patologie"