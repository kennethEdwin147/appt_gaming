@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Vérification d\'email requise') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="text-center">
                        <i class="bi bi-envelope-check display-1 text-primary mb-4"></i>
                        <h4 class="mb-3">Vérifiez votre adresse email</h4>
                        <p class="text-muted mb-4">
                            Nous avons envoyé un lien de vérification à votre adresse email. 
                            Veuillez cliquer sur le lien dans l'email pour activer votre compte.
                        </p>
                        
                        <div class="alert alert-info">
                            <strong>Important :</strong> Vous devez vérifier votre email avant de pouvoir vous connecter à votre compte.
                        </div>

                        <div class="mt-4">
                            <p class="text-muted small">
                                Vous n'avez pas reçu l'email ? Vérifiez votre dossier spam ou 
                                <a href="{{ route('login') }}" class="text-decoration-none">essayez de vous connecter à nouveau</a> 
                                pour renvoyer l'email de vérification.
                            </p>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-2"></i>Retour à la connexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
