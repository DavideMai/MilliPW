from django.urls import path
from . import views

urlpatterns = [
    path('', views.home_view, name='home'),
    path('cittadini/', views.cittadino_list_view, name='cittadino_list'),
    path('patologie/', views.patologia_list_view, name='patologia_list'),
    path('ricoveri/', views.ricovero_list_view, name='ricovero_list'),
    
    # Ospedale URLs
    path('ospedali/', views.ospedale_view, name='ospedale_list'),
    path('ospedali/new/', views.ospedale_view, name='ospedale_new'), # Re-uses the view for creating
    path('ospedali/<int:pk>/edit/', views.ospedale_view, name='ospedale_edit'),
    path('ospedali/<int:pk>/delete/', views.ospedale_delete_view, name='ospedale_delete'),
    path('ospedali/edit/cancel/', views.ospedale_cancel_edit, name='ospedale_cancel_edit'),
]