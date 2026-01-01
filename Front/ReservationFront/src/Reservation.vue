<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'


const router = useRouter()
const user = JSON.parse(localStorage.getItem('user'))
const salles = ref([])
const sallesTmp = ref([])
const equipements = ref([])
const reservations = ref([])
const equipementsSelected = ref([])
const idSalle = ref('')
const nbPersonnes = ref('')
const dateDebut = ref('')
const dateFin = ref('')
const response = ref('') 
const selectionEnCours = ref(false) 

const handleDateClick = (arg) => {
    const calendarApi = arg.view.calendar
    
    //si on est en semaine, on passe en jour
    if (calendarApi.view.type === 'dayGridWeek') {
        selectionEnCours.value = false
        calendarApi.changeView('timeGridDay', arg.dateStr)
        return
    }

    // on supprime les selections en cours si on la refait
    calendarApi.getEvents().forEach(event => {
        if (event.title === 'Ma réservation') event.remove()
    })

    if (!selectionEnCours.value) {
        //par défaut la durée est de 30 minutes
        const start = new Date(arg.date)
        const defaultEnd = new Date(start.getTime() + 30 * 60000)
        
        dateDebut.value = start.toISOString()
        dateFin.value = defaultEnd.toISOString()
        
        calendarApi.addEvent({
            title: 'Ma réservation',
            start: dateDebut.value,
            end: dateFin.value,
            allDay: arg.allDay,
            backgroundColor: '#4CAF50',
            borderColor: '#4CAF50'
        })
        
        selectionEnCours.value = true
    } else {
        //2ème clic : Définit une date de fin
        const start = new Date(dateDebut.value)
        const end = new Date(arg.date.getTime() + 30 * 60000) //bornes de clic inclusives

        if (end <= start) {
            dateDebut.value = ''
            dateFin.value = ''
            selectionEnCours.value = false
            return
        }

        dateDebut.value = start.toISOString()
        dateFin.value = end.toISOString()
        
        calendarApi.addEvent({
            title: 'Ma réservation',
            start: dateDebut.value,
            end: dateFin.value,
            backgroundColor: '#4CAF50',
            borderColor: '#4CAF50'
        })
        
        selectionEnCours.value = false
    }
}

//Configs du calendrier
const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
    initialView: 'dayGridWeek',
    headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridWeek'
    },
    editable: true,
    selectable: true,
    events: [],
    locale: 'fr',
    buttonText: {
        week: "Semaine"
    },
    allDayText: "Journée",
    validRange: {
        start: new Date().toISOString() //dates passées non valides
    },
    hiddenDays: [ 0, 6 ], //on cache les week-end
    dateClick: handleDateClick,
})

const logout = () => {
    localStorage.removeItem('user')
    router.push({ name: 'Login' })
}



onMounted(async () => {
    try {
        const responseSalle = await axios.post('http://localhost:8000/api/getSalles')
        salles.value = responseSalle.data
        sallesTmp.value = responseSalle.data
        const responseEquipement = await axios.post('http://localhost:8000/api/getEquipements')
        equipements.value = responseEquipement.data
        const responseReservation = await axios.post('http://localhost:8000/api/getReservations')
        reservations.value = responseReservation.data
        reservations.value.forEach(reservation => {
            calendarOptions.value.events.push({
                title: 'Réservation salle ' + reservation.idSalle,
                start: reservation.dateDebut,
                end: reservation.dateFin, 
                backgroundColor: 'red',
                borderColor: 'red'
            })
        })
    } catch (error) {
        let message = error.response?.data?.message || 'Une erreur est survenue, API Injoignable ?'
        alert('Erreur lors de la récupération des salles:', message)
    }
})

const handleValidation = async () => {
    try {
        console.log(dateDebut.value)
        console.log(dateFin.value)
        const res = await axios.post(`http://localhost:8000/api/addReservation`, {
            idUser: user.id,
            idSalle: idSalle.value,
            nbPersonnes: nbPersonnes.value,
            dateDebut: dateDebut.value,
            dateFin: dateFin.value
        })
        response.value = res.data
        alert('Reservation effectuée')
        router.push({ name: 'Home' })
    } catch (error) {
        console.error('Erreur API:', error)
        let errorMessage = 'Une erreur est survenue, API Injoignable ?'
        
        if (error.response && error.response.data) {
            errorMessage = error.response.data.message || error.response.data.error || JSON.stringify(error.response.data)
        }
        
        alert('Erreur lors de la réservation : ' + errorMessage)
    }
}

const filter = () => {

    if (nbPersonnes.value < 1) {
        nbPersonnes.value = 1
    }
    sallesTmp.value = salles.value
    let filteredSalles = salles.value.filter(salle => salle.capacite >= nbPersonnes.value)
    
    if (equipementsSelected.value.length > 0) {
        filteredSalles = filteredSalles.filter(salle => {
            return equipementsSelected.value.every(selectedId => 
                salle.equipements.some(equipement => equipement.id === selectedId.id)
            )
        })
    }   
    sallesTmp.value = filteredSalles
}


</script>

<template>
  <div class="header">
    <h1>ReservationSalle</h1>
    <button @click="logout" class="btnh">Se deconnecter</button>
  </div>
  <div class="reservation">
    <div class="form">
        <div style="display: flex; justify-content: center; margin-top: 1%;">
            <p>Reserver une salle</p>
        </div>
        <label for="nbPersonnes">Nombre de personnes</label>
        <input type="number" name="nbPersonnes" id="nbPersonnes" class="input" min="1" v-model="nbPersonnes" @change="filter">
        <label>Equipements</label>
        <multiselect
            v-model="equipementsSelected"
            :options="equipements"
            :multiple="true"
            label="nom"
            track-by="id"
            :close-on-select="false"
            placeholder=""
            @select="filter"
            @remove="filter"
        />
        <label for="salle">Salle</label>
        <select name="salle" id="salle" class="input" v-model="idSalle">
            <option v-for="salle in sallesTmp" :key="salle.id" :value="salle.id">{{ salle.nom }}</option>
        </select>
        <FullCalendar :options="calendarOptions" class="calendrier"/>
        <div class="buttons">
            <button @click="handleValidation" class="btn">Reserver</button>
            <button @click="router.push({ name: 'Home' })" class="btns">Annuler</button>
        </div>
    </div>
  </div>
</template>

<style scoped>
    *{
        font-weight: bold;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-sizing: border-box;
    }
    .header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #4CAF50;
        color: white;
        padding: 0 20px; 
    }
    .userLib{
        align-content: right;
    }
    .btnh{
        background-color: transparent;
        color: white;
        border: 1px solid white;
        border-radius: 5px;
        padding: 5px 15px;
        cursor: pointer;
    }
    .btnh:hover {
        background-color: white;
        color: #4CAF50;
    }
    .reservation {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .form {
	    position: absolute;
	    top: 50%;
	    left: 50%;
	    transform: translate(-50%, -50%);
	    background-color: #fff;
	    padding: 20px;
	    border-radius: 10px;
	    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .title {
	    text-align: center;
	    font-weight: bold;
	    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .input {
	    width: 100%;
	    height: 35px;
	    border: 1px solid #ccc;
	    border-radius: 5px;
	    padding: 5px;
	    margin-bottom: 10px;
    }
    .btn {
	    width: 100%;
	    height: 35px;
	    border: none;
	    border-radius: 5px;
	    background-color: #4CAF50;
	    color: white;  
        margin: 10px;
    }
    .btns {
	    width: 100%;
	    height: 35px;
	    border: none;
	    border-radius: 5px;
	    background-color: red;
	    color: white;  
        margin: 10px;       
    }
    .buttons{
        display: flex;
        justify-content: space-between;       
    }
    .equipements{
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .calendrier{
        max-height: 20em;
        overflow-y: scroll;
    }
</style>
