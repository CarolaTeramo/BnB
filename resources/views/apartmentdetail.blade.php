@extends('layouts.app')

@section('content')
  @if (session('message'))
    <div class="alert alert-success">
      {{ session('message') }}
    </div>
  @endif
  <div class="container-fluid no-gutters px-0">
    <div class="row no-gutters">
      <div class="col-md-6" style="height:500px;">{{-- COLONNA PRINCIPALE --}}
        @if (!empty($apartment->main_img))
          <img style="height:500px; width:100%; object-fit: cover;" src="{{ asset('storage/' . $apartment->main_img) }}" class="img-fluid" alt="Anteprima Appartamento">
          @else
          <img style="height:500px; width:100%; object-fit: cover;" src="https://kitv.images.worldnow.com/images/16468883_G.png?lastEditedDate=1522902908000" class="img-fluid" alt="Anteprima non disponibile">
        @endif
      </div>
      <div class="col-md-6" style="height:500px;"> {{-- COLONNA PRINCIPALE DOPPIE IMG --}}
        <div class="row no-gutters d-flex flex-wrap">
          @if (!empty($apartment_imgs))
              @forelse ($apartment_imgs as $img)
                <img style="height:250px; width:50%; object-fit: cover;" src="{{ asset('storage/' . $img->path) }}" alt="Anteprima Stanze Appartamento">
              @empty
                <img style="height:500px; width:100%; object-fit: cover;" src="https://ldonna.cdn-news30.it/blobs/full/e/6/4/6/e646c3b5-59e5-45d8-a16d-32a9778a81bd.jpg?_636392901355046092" class="img-fluid" alt="Anteprima non disponibile">
              @endforelse
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row mt-5">
      {{-- Titolo e Indirizzo Appartamento--}}
      {{-- <div class="row "> --}}
      <div class="col-12 p-0">
        <h1>{{ $apartment->title }}</h1>
      </div>
      <div class="col-4 p-0">
        <p>{{ $apartment->address }}</p>
          {{-- input hidden che prende valori da database e li passa a java --}}
          <input type="hidden" id="latitude" name="latitude" value="{{ $apartment->latitude}}" class="latitude_details">
          <input type="hidden" id="latitude" name="longitude" value="{{ $apartment->longitude}}" class="longitude_details">

      </div>
      <div class="row justify-content-between mt-3">
        <div class="col-md-7">
          {{-- Card Appartamento--}}
          <div class="card mb-3 info-appartamento">
            <div class="card-body">
              <p>{{ $apartment->description }}</p>
              <p>Servizi:<br>
                @foreach ($apartment->services as $service)
                  {{ $service->name }} @if (!$loop->last), @endif
                @endforeach
              </p>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">N. Stanze: {{ $apartment->n_rooms }}</li>
              <div class="row no-gutters">
                <div class="col-md-6">
                  <li class="list-group-item">Letti singoli: {{ $apartment->n_single_beds }}</li>
                </div>
                <div class="col-md-6">
                  <li class="list-group-item">Letti matrimoniali: {{ $apartment->n_double_beds }}</li>
                </div>
              </div>
              <li class="list-group-item">Prezzo per notte: {{ $apartment->price_per_night }}€</li>
            </ul>
          </div>

          @auth
            @if ($apartment->user_id == Auth::user()->id)
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-12 opzioni-appartamento">
                    <a class="btn btn-outline-secondary modifica-appartamento mr-2" href="{{ route('upr.apartments.edit', $apartment->id) }}">Modifica</a>

                    @if ($apartment->is_sponsored)
                      <a class="btn btn-warning mr-2">Sponsorizzazione attiva!!!</a>
                      @else
                      <a class="btn sponsorizza-appartamento mr-2" href="{{ route('upr.sponsor', $apartment->id) }}">Sponsorizza appartamento</a>
                    @endif
                      {{-- richiamo al modal per il messaggio di conferma --}}
                      <a class="btn btn-danger elimina-inserzione" data-toggle="modal" data-target="#exampleModal">Elimina inserzione</a>
                    </div>
                  {{-- modal messaggio conferma --}}
                  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">CONFERMA ELIMINA INSERZIONE</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <form action="{{ route('upr.apartments.destroy', $apartment->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                              <p class="text-center">Sei sicuro di voler eliminare questa inserzione?</p>
                              <p class="text-center">Una volta che avrai dato la conferma, non sarà possible tornare indietro</p>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                              <input class="btn btn-danger" type="submit" value="Elimina inserzione">
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mt-4">
                <div class="col-12">
                  <a href="{{ route('upr.apartments.statistics', $apartment->id) }}">Visualizza statitiche appartamento</a>
                </div>
              </div>
            @endif
          @endauth
        </div>

        <div class="col-md-5  ml-auto">
          {{-- Mappa --}}
            {{-- <img src="https://media.wired.com/photos/59269cd37034dc5f91bec0f1/master/pass/GoogleMapTA.jpg" class="card-img-top" alt="Anteprima non disponibile"> --}}

          <div id="map" class="map"></div>
          <div id="foldable" class="tt-overlay-panel -left-top -medium js-foldable"></div>
            {{-- <div class="card">
              <div class="card-body">
                <p class="card-text"><a href="#">{{ $apartment->address }}</a></p>
              </div>
            </div> --}}
        </div>
      </div>
    </div> {{--fine riga dettagli e mappa --}}

    {{-- FORM EMAIL --}}
    {{-- utente loggato --}}
    @auth
      <div class="row">
        <div class="col-md-8">
          @if ($apartment->user_id != Auth::user()->id)
            <hr class="mt-5">
            <h4 class="mt-3">Contatta il proprietario dell'appartamento</h4>
            <form class="mt-3" action="{{ route('store-message', $apartment->id) }}" method="post">
              @csrf
              <div class="form-group">
                <label for="name">Nome:</label>
                <input type="text" class="form-control" name="name" placeholder="Inserisci il tuo nome">
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Email:</label>
                  <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Inserisci la tua email" value="{{ Auth::user()->email }}">
                <small id="emailHelp" class="form-text text-muted">Non condivideremo la tua email con nessuno.</small>
              </div>
              <div class="form-group">
                <label for="subject">Oggetto:</label>
                <input type="text" class="form-control" name="subject" placeholder="Inserisci oggetto email">
              </div>
              <div class="form-group">
                <label for="message">Testo messaggio:</label>
                <textarea class="form-control" name="message" rows="6" placeholder="Scrivi un messaggio a proprietario di questo appartamento"></textarea>
              </div>
             <button type="submit" class="btn btn-primary">Invia</button>
            </form>
          @endif
        </div>
      </div>
    @endauth

    {{-- FORM EMAIL --}}
    {{-- utente non loggato --}}
    @guest
      <div class="row">
        <div class="col-md-8">
          <hr class="mt-5">
          <h4 class="mt-3">Contatta il proprietario dell'appartamento</h4>
          <form class="mt-3" action="{{ route('store-message', $apartment->id) }}" method="post">
            @csrf
            <div class="form-group">
              <label for="name">Nome:</label>
              <input type="text" class="form-control" name="name" placeholder="Inserisci il tuo nome">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Email:</label>
                <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Inserisci la tua email">
              <small id="emailHelp" class="form-text text-muted">Non condivideremo la tua email con nessuno.</small>
            </div>
            <div class="form-group">
              <label for="subject">Oggetto:</label>
              <input type="text" class="form-control" name="subject" placeholder="Inserisci oggetto email">
            </div>
            <div class="form-group">
              <label for="message">Testo messaggio:</label>
              <textarea class="form-control" name="message" rows="6" placeholder="Scrivi un messaggio a proprietario di questo appartamento"></textarea>
            </div>
           <button type="submit" class="btn btn-primary">Invia</button>
          </form>
        </div>
      </div>
    @endguest
  </div>
@endsection
