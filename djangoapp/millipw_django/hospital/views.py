from django.shortcuts import render, redirect, get_object_or_404
from django.db.models import Count, Q
from django.contrib import messages
from django.urls import reverse
from .models import Cittadino, Ospedale, Patologia, Ricovero
from .forms import OspedaleForm

# --- Main Pages ---

def home_view(request):
    """Renders the homepage."""
    context = {'active_page': 'home'}
    return render(request, 'hospital/index.html', context)

def cittadino_list_view(request):
    """Handles searching and listing for Cittadini."""
    queryset = Cittadino.objects.all()
    
    # Dynamically build filter conditions from GET parameters
    filters = Q()
    if query := request.GET.get('CSSN', '').strip():
        filters &= Q(cssn__icontains=query)
    if query := request.GET.get('Nome', '').strip():
        filters &= Q(nome__icontains=query)
    if query := request.GET.get('Cognome', '').strip():
        filters &= Q(cognome__icontains=query)
    if query := request.GET.get('Indirizzo', '').strip():
        filters &= Q(indirizzo__icontains=query)
    if query := request.GET.get('NumeroCivico', '').strip():
        filters &= Q(numero_civico__icontains=query)
    if query := request.GET.get('LuogoNascita', '').strip():
        filters &= Q(luogo_nascita__icontains=query)
    if query := request.GET.get('DataNascita', '').strip():
        filters &= Q(data_nascita=query)

    queryset = queryset.filter(filters)

    context = {
        'cittadini': queryset,
        'active_page': 'cittadini',
        'get_params': request.GET, # Pass GET params to pre-fill form
    }
    return render(request, 'hospital/cittadini.html', context)

def patologia_list_view(request):
    """Handles searching and listing for Patologie, with ricoveri count."""
    # Annotate adds a 'numero_ricoveri' field to each patologia object
    queryset = Patologia.objects.annotate(numero_ricoveri=Count('ricoveri'))
    
    filters = Q()
    if query := request.GET.get('NomePatologia', '').strip():
        filters &= Q(nome_patologia__icontains=query)
    if query := request.GET.get('Tipologia', '').strip():
        filters &= Q(tipologia__icontains=query)
        
    queryset = queryset.filter(filters)

    context = {
        'patologie': queryset,
        'active_page': 'patologie',
        'get_params': request.GET,
    }
    return render(request, 'hospital/patologie.html', context)

def ricovero_list_view(request):
    """Handles searching and listing for Ricoveri."""
    # Use select_related and prefetch_related to optimize database queries
    # This prevents the N+1 query problem from the original PHP code
    queryset = Ricovero.objects.select_related('cittadino', 'ospedale').prefetch_related('patologie')

    filters = Q()
    if query := request.GET.get('CSSNCittadino', '').strip():
        filters &= Q(cittadino__cssn__icontains=query)
    if query := request.GET.get('NomeOspedale', '').strip():
        filters &= Q(ospedale__nome_ospedale__icontains=query)
    if query := request.GET.get('DataRicovero', '').strip():
        filters &= Q(data_ricovero=query)
    if query := request.GET.get('DurataRicovero', '').strip():
        filters &= Q(durata_ricovero=query)
    if query := request.GET.get('CostoRicovero', '').strip():
        filters &= Q(costo_ricovero=query)
    if query := request.GET.get('MotivoRicovero', '').strip():
        filters &= Q(motivo_ricovero__icontains=query)
    
    queryset = queryset.filter(filters)

    context = {
        'ricoveri': queryset,
        'active_page': 'ricoveri',
        'get_params': request.GET,
    }
    return render(request, 'hospital/ricoveri.html', context)

# --- Ospedale CRUD Views ---

def ospedale_view(request, pk=None):
    """
    A single view to handle listing, creating, and updating Ospedali.
    This mirrors the logic of the original ospedale.php file.
    - If `pk` is provided, we are in "edit" mode.
    - If no `pk`, we are in "create" mode.
    """
    instance = None
    if pk:
        instance = get_object_or_404(Ospedale, pk=pk)

    if request.method == 'POST':
        form = OspedaleForm(request.POST, instance=instance)
        if form.is_valid():
            form.save()
            success_message = "Ospedale modificato con successo!" if instance else "Nuovo ospedale aggiunto con successo!"
            messages.success(request, success_message)
            return redirect('ospedale_list')
        else:
            # If form is invalid, add an error message. The invalid form will be
            # re-rendered in the template, showing specific field errors.
            error_message = "Errore nella modifica." if instance else "Errore nell'inserimento."
            messages.error(request, f"{error_message} Controlla i campi del modulo.")
    else:
        # For GET request, create a new form or one pre-filled with instance data
        form = OspedaleForm(instance=instance)

    # --- Search and List Logic (always runs) ---
    queryset = Ospedale.objects.annotate(numero_ricoveri=Count('ricovero')).order_by('id_ospedale')
    
    filters = Q()
    if query := request.GET.get('NomeOspedale', '').strip():
        filters &= Q(nome_ospedale__icontains=query)
    if query := request.GET.get('Citta', '').strip():
        filters &= Q(citta__icontains=query)
    if query := request.GET.get('Indirizzo', '').strip():
        filters &= Q(indirizzo__icontains=query)

    queryset = queryset.filter(filters)

    context = {
        'ospedali': queryset,
        'form': form,
        'edit_instance': instance, # Will be None in create mode
        'active_page': 'ospedali',
        'get_params': request.GET,
    }
    return render(request, 'hospital/ospedale.html', context)


def ospedale_delete_view(request, pk):
    """Handles deleting an Ospedale."""
    ospedale = get_object_or_404(Ospedale, pk=pk)
    try:
        ospedale.delete()
        messages.success(request, f"Ospedale '{ospedale.nome_ospedale}' eliminato con successo!")
    except Exception as e:
        # This will catch the ProtectedError if a director is still assigned
        messages.error(request, f"Errore durante l'eliminazione: {e}. L'ospedale potrebbe essere ancora in uso o protetto.")
    return redirect('ospedale_list')

def ospedale_cancel_edit(request):
    """Handles the 'Annulla' button when editing."""
    return redirect('ospedale_list')