function initSelect(inputName)
{
    document.querySelector('.select-wrapper').addEventListener('click', function() {
        this.querySelector('.select').classList.toggle('open');
    });

    for (const option of document.querySelectorAll(".custom-option")) {
        if ($(inputName).val() == option.dataset["value"])
        {
            option.classList.add('selected');
            option.closest('.select').querySelector('.select__trigger span').textContent = option.textContent;
        }
        option.addEventListener('click', function() {
            if (!this.classList.contains('selected')) {
                if (this.parentNode.querySelector('.custom-option.selected'))
                    this.parentNode.querySelector('.custom-option.selected').classList.remove('selected');
                this.classList.add('selected');
                this.closest('.select').querySelector('.select__trigger span').textContent = this.textContent;
                $(inputName).val(this.dataset["value"]);
            }
            else
            {
                this.parentNode.querySelector('.custom-option.selected').classList.remove('selected');
                this.closest('.select').querySelector('.select__trigger span').textContent = 'Seleccione';
                $(inputName).val('');
            }
        })
    };

    window.addEventListener('click', function(e) {
        const select = document.querySelector('.select')
        if (!select.contains(e.target)) {
            select.classList.remove('open');
        }
    });
};
