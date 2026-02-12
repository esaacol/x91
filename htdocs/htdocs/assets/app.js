function placeBet(matchId, outcome) {

    let stake = prompt("¿Cuánto deseas apostar?");
    if (!stake) return;

    fetch("api/create_bet.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            user_id: "USER_ID_DE_PRUEBA",
            match_id: matchId,
            selected_outcome: outcome,
            stake: stake
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || data.error);
    });
}
