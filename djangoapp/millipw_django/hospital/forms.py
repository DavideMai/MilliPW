from django import forms
from .models import Ospedale, Cittadino

class OspedaleForm(forms.ModelForm):
    # Make the field a choice field for better UX
    codice_sanitario_direttore = forms.ModelChoiceField(
        queryset=Cittadino.objects.all(),
        label="Direttore Sanitario (CSSN)",
        required=True,
        widget=forms.Select(attrs={'class': 'form-control'})
    )

    class Meta:
        model = Ospedale
        fields = [
            'nome_ospedale', 'indirizzo', 'numero_civico',
            'citta', 'numero_telefono', 'codice_sanitario_direttore'
        ]
        labels = {
            'nome_ospedale': 'Nome Ospedale',
            'indirizzo': 'Indirizzo',
            'numero_civico': 'Numero Civico',
            'citta': 'Città',
            'numero_telefono': 'Numero Telefonico',
        }
        widgets = {
            'nome_ospedale': forms.TextInput(attrs={'required': True, 'class': 'form-control'}),
            'indirizzo': forms.TextInput(attrs={'required': True, 'class': 'form-control'}),
            'numero_civico': forms.NumberInput(attrs={'required': True, 'class': 'form-control'}),
            'citta': forms.TextInput(attrs={'required': True, 'class': 'form-control'}),
            'numero_telefono': forms.TextInput(attrs={
                'required': True, 
                'pattern': '[0-9]*', 
                'inputmode': 'tel',
                'class': 'form-control'
            }),
        }

    def clean_codice_sanitario_direttore(self):
        """
        Custom validation to ensure a director is not assigned to more than one hospital.
        This replicates the `isCSDtaken` PHP function.
        """
        direttore = self.cleaned_data.get('codice_sanitario_direttore')
        
        # Build a query for Ospedali that use this direttore
        query = Ospedale.objects.filter(codice_sanitario_direttore=direttore)
        
        # If we are editing an existing Ospedale instance, we must exclude it from the check.
        # `self.instance` is the object being edited. `pk` is its primary key.
        if self.instance and self.instance.pk:
            query = query.exclude(pk=self.instance.pk)

        # If any other hospital is found with this director, raise a validation error.
        if query.exists():
            raise forms.ValidationError(
                "Errore: Il codice sanitario del direttore fornito è già in uso da un altro ospedale."
            )
        return direttore