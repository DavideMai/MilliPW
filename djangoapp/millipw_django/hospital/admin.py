# hospital/admin.py
from django.contrib import admin
from .models import Cittadino, Ospedale, Patologia, Ricovero, RicoveroPatologie

# Register your models here to make them accessible in the Django admin interface.
admin.site.register(Cittadino)
admin.site.register(Ospedale)
admin.site.register(Patologia)
admin.site.register(Ricovero)
admin.site.register(RicoveroPatologie)