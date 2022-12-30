class ButtonLoader extends UI.View {

    constructor(elementId, elementText) {
        super();
        this.element = this.getNodeById(elementId);
        this.elementText = elementText;
    }

    loadButtonState(trackText="") {
        this.element.innerHTML = "";
        this.element.appendChild(
            this.newTag('i')({
                class: `fa fa-spinner fa-spin fa-2x`
            })
        );
        if(!Utility.ObjectTypes.empty(trackText)) {
            this.element.appendChild(
                this.newTag('span')({
                    text: ` ${trackText}`
                })
            );
        }
    }

    refreshStateButton(hasIcon=false,iconName="") {
        this.element.innerHTML = "";
        if(hasIcon) {
            this.element.appendChild(
                this.newTag('i')({
                    class: `${iconName}`
                })
            );
            this.element.appendChild(
                this.newTag('span')({
                    text: ` ${this.elementText}`
                })
            );
        }
        else this.element.innerHTML = this.elementText
    }

}

class AppSnackbar {

    static showMessageBox(state,message) {
        let color = state === true ? '#36bd78' :'#e53038';
        AppSnackbar.__snackBar(message,color);
    }

    static __snackBar(message,color) {
        Snackbar.show({
            text: message,
            pos: 'top-center',
            showAction: false,
            actionText: "Dismiss",
            duration: 8000,
            textColor: '#fff',
            backgroundColor: color
        }); 
    }
}
(function() {
    let snackBarMessage = document.getElementById('snackBarMessage');
    let snackBarState   = document.getElementById('snackBarState');

    if(snackBarMessage !== null && snackBarState !== null) {
        let isSuccess = snackBarState.value.toLowerCase() === 'success';
        let isError   = snackBarState.value.toLowerCase() === 'error';
        let hasMessage = snackBarMessage.value.trim() !== '';
        let message = snackBarMessage.value.trim();
        if(isSuccess && hasMessage) {
            AppSnackbar.showMessageBox(isSuccess,message);
        } else if (isError && hasMessage) {
            AppSnackbar.showMessageBox(!isError,message);
        }
    }
})();
