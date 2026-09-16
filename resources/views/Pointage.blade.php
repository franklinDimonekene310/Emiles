<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pointage pro</title>
    <link rel="stylesheet" href="{{asset('style2.css')}}" type="text/css">   
    
     {{-- Lien pour select2   --}}
    <link rel="stylesheet" href="{{asset('select2\cdn.jsdelivr.css')}}" type="text/css">  
</head>
<body>
    <div id="id01" class="modal">                
                <form class="modal-container" id ="pointageForm" method="GET" >
                    @csrf
                    <span onclick="fermerModal()" class="modal-close-btn" title="Fermer">&times;</span>
                    <div class="container">
                       
                        <div class="modal-title">Pointage manquant</div>
                        <div class="form-group-row">
                            {{-- <label for="debutDecade">Début décade du </label> --}}
                            <div class="form-field">
                                <input class="form-input" type="date" id="debutDecade" name="debutDecade" value="{{ old('debutDecade') }}">
                            </div>

                            <div class="form-field" style="flex: 0 0 30px; align-items: center; justify-content: center; color: #a0aec0;">
                               au 
                            </div>

                            <div class="form-field">                                               
                                <input class="form-input" type="date" id="finDecade" name="finDecade" value="{{ old('finDecade') }}">
                            </div>                           
                        </div>

                        <div class="form-group-row">
                            <div class="form-field">
                                <label for="direction" class="form-label">Directions</label>                
                                <select class="form-input" name="directions[]" id="direction" multiple>                                
                                    <option value="00">TOUS</option>
                                    <option value="01">DIR GEN</option>
                                    <option value="03">APPROS</option>                                    
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="contrat" class="form-label">Contrats</label>                
                                <select class="form-input" name="contrats[]" id="contrat" multiple>                                
                                    <option value="00">TOUS</option>
                                    <option value="0">PERMANENTS</option>
                                    <option value="1">SAISONNIERS</option>
                                </select>
                            </div>                            
                        </div>
                        <div class="form-group-row">
                            <div class="form-field">
                                <label for="grade" class="form-label">Grades</label>                
                                <select class="form-input" name="grades[]" id="grade" multiple>                                
                                    <option value="00">TOUS</option>
                                    <option value="01">TA</option>
                                    <option value="02">ML</option>                               
                                </select>
                            </div>                            
                        </div> 
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary">Annuler</button>
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </div>
                    </div>
                </form>
        </div>

        {{-- SCRIPT POUR SELECT 2 --}}
        <script src="{{ asset('select2\code.jquery.js') }}"></script>
        <script src="{{ asset('select2\cdn.jsdelivr.js') }}"></script>
</body>
</html>