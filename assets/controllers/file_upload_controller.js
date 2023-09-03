import {Controller} from '@hotwired/stimulus';
import {createModal} from "../app";

export default class extends Controller {

    connect() {

        // Création de la modal pour les éditions de données
        const modalEdit = createModal(document.getElementById('edit-data'));

        // Récupération de l'URL du site
        let websiteroot = this.element.dataset.fileUploadWebsiterootValue;

        // Ajout des évènements
        document.getElementById("uploadFile").addEventListener("click", uploadFile);
        document.getElementById("dropDataBase").addEventListener("click", dropDataBase);
        document.getElementById("nextDatas").addEventListener("click", nextDatas);
        document.getElementById("previousDatas").addEventListener("click", previousDatas);
        document.getElementById("closeModal").addEventListener("click", function () {
            modalEdit.hide();
        });
        document.getElementById("saveModal").addEventListener("click", function () {
            saveDatas(document.getElementById("saveModal").dataset.id);
        });
        document.addEventListener('click', function (event) {
            if (event.target.matches('.deleteData')) {
                deleteData(event.target.dataset.id);
            } else if (event.target.matches('.editData')) {
                editData(event.target.dataset.id);
            }
        });

        // Récupération des données au chargement de la page
        $(document).ready(function () {
            $.ajax({
                url: websiteroot + '/load/datas',
                type: 'POST',
                dataType: 'json',
                data: {
                    step: 5,
                    offset: 0
                },
                success: function (data) {
                    displayDatas(data.musicBands);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        async function uploadFile() {
            let formData = new FormData();
            let fileupload = document.getElementById("fileupload");
            let uploadLoading = document.getElementById("uploadLoading");
            let uploadDoneLoading = document.getElementById("uploadDoneLoading");

            formData.append("file", fileupload.files[0]);

            // Vérification si il n'y a pas de fichier, si il est trop lourd ou si il est pas au bon format
            if (fileupload.files.length === 0) {
                alert('Aucun fichier sélectionné');
            } else if (fileupload.files[0].size / 1000 > 1500) {
                alert('Fichier trop lourd, veuillez sélectionner un fichier de moins de 1,5 Mo');
            } else if (fileupload.files[0]['type'] !== 'application/vnd.ms-excel' && fileupload.files[0]['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                alert('Format de fichier non valide, veuillez sélectionner un fichier .xls ou .xlsx');
            } else {

                uploadLoading.classList.remove('hidden');

                const response = await fetch(websiteroot + '/import/file/upload', {
                    method: "POST",
                    dataType: 'json',
                    async: true,
                    body: formData
                });

                const data = await response.json();

                setTimeout(function () {
                    uploadLoading.classList.add('hidden');
                    uploadDoneLoading.classList.remove('hidden');

                    setTimeout(function () {
                        uploadDoneLoading.classList.add('hidden');
                        // On recharge les données du tableau
                        $.ajax({
                            url: websiteroot + '/load/datas',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                step: 5,
                                offset: 0
                            },
                            success: function (data) {
                                document.getElementById("thead-musicBands").innerHTML = "";
                                document.getElementById("tbody-musicBands").innerHTML = "";
                                displayDatas(data.musicBands);
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });
                    }, 1000);
                }, 1000);

            }
        }

        async function dropDataBase() {

            let dropLoading = document.getElementById("dropLoading");
            let dropDoneLoading = document.getElementById("dropDoneLoading");

            dropLoading.classList.remove('hidden');

            const response = await fetch(websiteroot + '/import/database/drop', {
                method: "POST",
                dataType: 'json',
                async: true
            });

            const data = await response.json();

            setTimeout(function () {
                dropLoading.classList.add('hidden');
                dropDoneLoading.classList.remove('hidden');
                document.getElementById("thead-musicBands").innerHTML = "";
                document.getElementById("tbody-musicBands").innerHTML = "";
                document.getElementById("nextDatas").classList.add('hidden');
                document.getElementById("previousDatas").classList.add('hidden');
                setTimeout(function () {
                    dropDoneLoading.classList.add('hidden');
                }, 1000);
            }, 1000);
        }

        async function deleteData(id) {

            $.ajax({
                url: websiteroot + '/import/data/delete',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (data) {
                    // Après suppression, on recharge les données du tableau
                    $.ajax({
                        url: websiteroot + '/load/datas',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            step: 5,
                            offset: 0
                        },
                        success: function (data) {
                            document.getElementById("thead-musicBands").innerHTML = "";
                            document.getElementById("tbody-musicBands").innerHTML = "";
                            document.getElementById("nextDatas").classList.add('hidden');
                            document.getElementById("previousDatas").classList.add('hidden');
                            displayDatas(data.musicBands);
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        async function previousDatas() {

            // Récupération de l'offset
            let offset = document.getElementById("table-musicBands").dataset.offset;

            if (offset > 0) {
                $.ajax({
                    url: websiteroot + '/load/datas',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        step: 5,
                        offset: Number(offset) - 5
                    },
                    success: function (data) {
                        if (data.musicBands.length > 0) {
                            // On met à jour l'offset et on recharge les données du tableau
                            document.getElementById("table-musicBands").dataset.offset = (Number(offset) - 5).toString();
                            document.getElementById("thead-musicBands").innerHTML = "";
                            document.getElementById("tbody-musicBands").innerHTML = "";
                            displayDatas(data.musicBands);
                        }
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        }

        async function nextDatas() {

            // Récupération de l'offset
            let offset = document.getElementById("table-musicBands").dataset.offset;

            $.ajax({
                url: websiteroot + '/load/datas',
                type: 'POST',
                dataType: 'json',
                data: {
                    step: 5,
                    offset: Number(offset) + 5
                },
                success: function (data) {
                    if (data.musicBands.length > 0) {
                        // On met à jour l'offset et on recharge les données du tableau
                        document.getElementById("table-musicBands").dataset.offset = (Number(offset) + 5).toString();
                        document.getElementById("thead-musicBands").innerHTML = "";
                        document.getElementById("tbody-musicBands").innerHTML = "";
                        displayDatas(data.musicBands);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        async function editData(id) {
            $.ajax({
                url: websiteroot + '/edit/data',
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id
                },
                success: function (data) {
                    // On met à jour le modal et on l'affiche
                    document.getElementById("titleModal").innerHTML = JSON.parse(data).musicBandName;
                    document.getElementById("bodyModal").innerHTML = JSON.parse(data).modal;
                    document.getElementById("saveModal").setAttribute('data-id', 'formId-' + JSON.parse(data).musicBandId);
                    modalEdit.show();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        async function saveDatas(id) {
            $.ajax({
                url: websiteroot + '/update/data',
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                    form: JSON.parse(JSON.stringify($('form').serializeArray()))
                },
                success: function (data) {
                    // Après sauvegarde, on recharge les données du tableau et on ferme le modal
                    modalEdit.hide();
                    $.ajax({
                        url: websiteroot + '/load/datas',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            step: 5,
                            offset: 0
                        },
                        success: function (data) {
                            document.getElementById("table-musicBands").dataset.offset = "0";
                            document.getElementById("thead-musicBands").innerHTML = "";
                            document.getElementById("tbody-musicBands").innerHTML = "";
                            displayDatas(data.musicBands);
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

        function displayDatas(datas) {

            let theadMusicBands = document.getElementById("thead-musicBands");
            let tbodyMusicBands = document.getElementById("tbody-musicBands");

            datas.forEach(function (musicBand) {

                //Création des THEAD pour le responsive
                let trHead = document.createElement("tr");
                trHead.id = "thead-musicBands-" + musicBand.id;
                trHead.classList.add("bg-teal-400", "flex", "flex-col", "flex-no", "wrap", "lg:table-row", "rounded-l-lg", "lg:rounded-none", "mb-2", "lg:mb-0");
                theadMusicBands.appendChild(trHead);

                let th1 = document.createElement("th");
                th1.innerHTML = "Nom du groupe";
                th1.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th1);

                let th2 = document.createElement("th");
                th2.innerHTML = "Origine";
                th2.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th2);

                let th3 = document.createElement("th");
                th3.innerHTML = "Ville";
                th3.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th3);

                let th4 = document.createElement("th");
                th4.innerHTML = "Année début";
                th4.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th4);

                let th5 = document.createElement("th");
                th5.innerHTML = "Année séparation";
                th5.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th5);

                let th6 = document.createElement("th");
                th6.innerHTML = "Fondateurs";
                th6.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th6);

                let th7 = document.createElement("th");
                th7.innerHTML = "Membres";
                th7.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th7);

                let th8 = document.createElement("th");
                th8.innerHTML = "Courant musical";
                th8.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th8);

                let th9 = document.createElement("th");
                th9.innerHTML = "Présentation";
                th9.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th9);

                let th10 = document.createElement("th");
                th10.innerHTML = "Action";
                th10.classList.add("px-3", "py-3", "text-left", "whitespace-nowrap");
                trHead.appendChild(th10);

                //Ajout des données dans le tableau
                let tr = document.createElement("tr");
                tr.id = "tbody-musicBands-" + musicBand.id;
                tr.classList.add("flex", "flex-col", "flex-no", "wrap", "lg:table-row", "mb-2", "lg:mb-0")
                tbodyMusicBands.appendChild(tr);

                let td1 = document.createElement("td");
                td1.innerHTML = '<button data-id="' + musicBand.id + '" class="editData">' + musicBand.name + '</button>';
                td1.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3", "font-medium", "text-gray-900", "whitespace-nowrap");
                tr.appendChild(td1);

                let td2 = document.createElement("td");
                td2.innerHTML = musicBand.origin;
                td2.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                tr.appendChild(td2);

                let td3 = document.createElement("td");
                td3.innerHTML = musicBand.city;
                td3.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                tr.appendChild(td3);

                let td4 = document.createElement("td");
                td4.innerHTML = musicBand.startYear.date.slice(0, 4);
                td4.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                tr.appendChild(td4);

                let td5 = document.createElement("td");
                td5.innerHTML = musicBand.endYear ? musicBand.endYear.date.slice(0, 4) : 'Non renseigné';
                td5.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                if (musicBand.endYear === null || musicBand.endYear === 'undefined') {
                    td5.classList.add("text-red-500");
                }
                tr.appendChild(td5);

                let td6 = document.createElement("td");
                td6.innerHTML = musicBand.founders ?? 'Non renseigné';
                td6.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                if (musicBand.founders === null || musicBand.founders === 'undefined') {
                    td6.classList.add("text-red-500");
                }
                tr.appendChild(td6);

                let td7 = document.createElement("td");
                td7.innerHTML = musicBand.members ?? 'Non renseigné';
                td7.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                if (musicBand.members === null || musicBand.members === 'undefined') {
                    td7.classList.add("text-red-500");
                }
                tr.appendChild(td7);

                let td8 = document.createElement("td");
                td8.innerHTML = musicBand.musicalStyle ?? 'Non renseigné';
                td8.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                if (musicBand.musicalStyle === null || musicBand.musicalStyle === 'undefined') {
                    td8.classList.add("text-red-500");
                }
                tr.appendChild(td8);

                let td9 = document.createElement("td");
                td9.innerHTML = musicBand.description ? musicBand.description.slice(0, 20) + ' ...' : 'Non renseigné';
                td9.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-3");
                if (musicBand.description === null || musicBand.description === 'undefined') {
                    td9.classList.add("text-red-500");
                }
                tr.appendChild(td9);

                let td10 = document.createElement("td");
                td10.innerHTML = '<button data-id="' + musicBand.id + '" class="deleteData btn btn-red text-xs">\n' +
                    'Supprimer\n' +
                    '</button>';
                td10.classList.add("border-grey-light", "border", "hover:bg-gray-100", "p-2");
                tr.appendChild(td10);

                document.getElementById("nextDatas").classList.remove('hidden');
                document.getElementById("previousDatas").classList.remove('hidden');
            });
        }
    }
}