<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tink</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body>

<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal">Hello World</h5>
</div>

<div id="app" class="container">

    <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <h1 class="display-4">Выберите товары</h1>
        <p class="lead">Они очень крутые</p>
    </div>

    <div class="card-deck mb-3 text-center">
        <div v-for="item in items" :key="item.id" class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">
                    {{ item.name }}
                    <span @click="removeFromBag(item)" class="badge badge-warning">{{ getItemCount(item) }} </span>
                </h4>
            </div>
            <div class="card-body">
                <h1 class="card-title pricing-card-title">{{ item.price }}</h1>
                <div class="mt-3 mb-4">{{ item.description }}</div>

                <button type="button"
                        @click="addToBag(item)"
                        class="btn btn-lg btn-block btn-outline-primary">Добавить</button>
            </div>
        </div>
    </div>

    <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <p class="lead">Итого: {{ sum }}</p>
        <button :disabled="sum < 1 || loading" @click="toBuy" class="btn btn-lg btn-primary">Оплатить</button>
    </div>

</div>

<script>
    const app = new Vue({
        el: '#app',

        data: () => ({
            items: [
                { title: 'First', price: 10,  }
            ],
            bag: [],
            loading: false,
        }),

        computed: {
            sum () {
                return this.bag.map(i => this.items.find(item => i === item.id))
                    .reduce((prev, item) => prev + item.price, 0)
            },
        },

        methods: {
            addToBag (item) {
                this.bag.push(item.id)
            },

            removeFromBag (item) {
                let index = this.bag.findIndex(i => item.id === i)
                if (index < 0) {
                    return
                }
                this.bag.splice(index, 1)
            },

            getItemCount (item) {
                return this.bag.filter(id => item.id === id).length
            },

            toBuy () {
                this.loading = true
                axios.post('/items.php',{
                    bag: this.bag
                })
                    .finally(() => {
                        this.loading = false
                    })
            },
        },

        mounted () {
            axios('/items.php')
                .then(({data}) => {
                    this.items = data.items
                })
        }
    })
</script>

</body>
</html>