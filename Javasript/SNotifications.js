document.addEventListener("DOMContentLoaded", function () {
    const messageList = document.getElementById("messageList");
    const messageInput = document.getElementById("messageInput");
    const sendMessageButton = document.getElementById("sendMessage");
    
    sendMessageButton.addEventListener("click", function () {
        const messageText = messageInput.value.trim();
        if (messageText === "") return;
        
        // Create a new message element
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("p-2", "bg-primary", "text-white", "rounded", "mb-2", "text-end");
        messageDiv.innerHTML = `<strong>You:</strong> ${messageText}`;
        
        messageList.appendChild(messageDiv);
        messageInput.value = "";
        messageList.scrollTop = messageList.scrollHeight;
        
        // Simulate a reply from HR after 1.5s
        setTimeout(() => {
            const replyDiv = document.createElement("div");
            replyDiv.classList.add("p-2", "bg-light", "rounded", "mb-2");
            replyDiv.innerHTML = `<strong>HR Manager:</strong> Thank you for your message! We will get back to you soon.`;
            
            messageList.appendChild(replyDiv);
            messageList.scrollTop = messageList.scrollHeight;
        }, 1500);
    });
    
    // Send message on pressing Enter key
    messageInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            sendMessageButton.click();
        }
    });
});
