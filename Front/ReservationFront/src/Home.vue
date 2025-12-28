<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = JSON.parse(localStorage.getItem('user'))
const reservations = ref([])  
const salles = ref([])
const logout = () => {
    localStorage.removeItem('user')
    router.push({ name: 'Login' })
}

onMounted(async () => {
    try {
        const responseResa = await axios.post('http://localhost:8000/api/getReservationsByUser', {
            idUser: user.id
        })
        reservations.value = responseResa.data
        const responseSalle = await axios.post('http://localhost:8000/api/getSalles')
        salles.value = responseSalle.data
    } catch (error) {
        console.error('Erreur lors de la récupération des réservations:', error)
    }
})

const deleteReservation = async (id) => {
    try {
        await axios.post(`http://localhost:8000/api/deleteReservation`, {
            id: id
        })
        reservations.value = reservations.value.filter(reservation => reservation.id !== id)
    } catch (error) {
        console.error('Erreur lors de la suppression de la réservation:', error)
    }
}
function formatDate(dateStr) {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    if (isNaN(date.getTime())) return dateStr
    
    return new Intl.DateTimeFormat("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    }).format(date)
}
</script>

<template>
    
    <div class="header">
        <h1>ReservationSalle</h1>
        <button @click="logout" class="btnh">Se deconnecter</button>
    </div>
    <div class="reservation-panel">
        <p>Reservations</p> 
        <div class="reservations-header">
            <p>Nom de salle</p> 
            <p>Date de début</p>
            <p>Date de fin</p>
            <p>Actions</p>
        </div>
        <div v-for="reservation in reservations" :key="reservation.id" class="reservations">
            <p :title="salles.find(salle => salle.id === reservation.idSalle).description">{{ salles.find(salle => salle.id === reservation.idSalle).nom }}</p> 
            <p>{{ formatDate(reservation.dateDebut) }}</p>
            <p>{{ formatDate(reservation.dateFin) }}</p>
            <button @click="deleteReservation(reservation.id)" class="btns">Supprimer</button>
        </div>
        <div style="display: flex; justify-content: center; margin-top: 1%;">
            <button @click="router.push({ name: 'Reservation' })" class="btn">Reserver une salle</button>
        </div>
    </div>
    <div class="reservation-panel">
        <p>Salles Disponibles actuellement</p>
        <div class="reservations-header">
            <p>Nom de salle</p> 
            <p>Capacité</p>
        </div>
        <div v-for="salle in salles" :key="salle.id" class="reservations">
            <p>{{ salle.nom }}</p> 
            <p>{{ salle.capacite }} personnes</p>
        </div>   
    </div>
</template>

<style scoped>
    *{
        font-weight: bold;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #4CAF50;
        color: white;
        padding: 0 20px; 
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    .btn{
        background-color: transparent;
        color: #4CAF50;
        border: 1px solid #4CAF50;
        border-radius: 5px;
        padding: 5px 15px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #4CAF50;
        color: white;
    }
    .btns{
        background-color: transparent;
        color: red;
        border: 1px solid red;
        border-radius: 5px;
        padding: 5px 15px;
        cursor: pointer;
    }
    .btns:hover {
        background-color: red;
        color: white;
    }
    .user {
        padding: 20px;
    }
    .reservations{
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        justify-content: space-between;
        align-items: center;
        background-color: #fff;
        padding: 20px;
        border-bottom: 1px solid #ccc;
    }
    .reservations-header{
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ccc;
        padding-left: 20px;
        padding-right: 20px;
    }
    .reservation-panel{
        border: 1px solid #ccc;
        padding-right: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding-left: 20px;
        padding-bottom: 2%;
        margin-top: 5%;
        margin-left: 10%;
        margin-right: 10%;
        margin-bottom: 5%;
    }
</style>
