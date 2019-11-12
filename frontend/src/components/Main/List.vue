<template>
  <div>
  <app-dialog :message="message" title="Alguma coisa aconteceu" :value="dialog" v-model="dialog"></app-dialog>
    <v-card id="main" class="mx-auto" max-width="800" :loading="loading">
      <v-img
        class="white--text align-end"
        height="200px"
        src="https://images.unsplash.com/photo-1538370965046-79c0d6907d47?ixlib=rb-1.2.1&w=1000&q=80"
      >
        <v-card-title>Catálogo de meteóro da NASA</v-card-title>
      </v-img>
      <v-card-text class="text--primary">
        <span class="group pa-2">
          <v-row>
            <v-col cols="12" sm="6" md="6">
              <v-menu
                v-model="menu1"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="startDate"
                    label="Data Início"
                    prepend-icon="event"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="startDate" @input="menu1 = false"></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="12" sm="6" md="6">
              <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="endDate"
                    label="Data Final"
                    prepend-icon="event"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker @change="getMetoers()" v-model="endDate" @input="menu2 = false"></v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
          <v-text-field
            @keyup="findByName($event)"
            label="Buscar"
            prepend-icon="search"
            v-model="search"
          ></v-text-field>
        </span>
        <v-expansion-panels>
          <v-expansion-panel v-for="(item,index) in meteor" :key="index">
            <v-expansion-panel-header>{{item.name}}</v-expansion-panel-header>
            <v-expansion-panel-content>
              <li>
                <span class="font-weight-black">Data de aproximação</span>
                : {{item.info.close_approach_date}}
              </li>
              <li>
                <span class="font-weight-black">Velocidade Relativa</span>:
                <ul>
                  <li>
                    <span class="font-weight-bold">KM/S</span>
                    :{{item.info.relative_velocity.kilometers_per_second}}
                  </li>
                  <li>
                    <span class="font-weight-bold">KM/S</span>
                    :{{item.info.relative_velocity.kilometers_per_hour}}
                  </li>
                  <li>
                    <span class="font-weight-bold">KM/S</span>
                    :{{item.info.relative_velocity.miles_per_hour}}
                  </li>
                </ul>
              </li>

              <li>
                <span class="font-weight-black">Distância Perdida</span>:
                <ul>
                  <li>
                    <span class="font-weight-bold">Astronômica</span>
                    :{{item.info.miss_distance.astronomical}}
                  </li>
                  <li>
                    <span class="font-weight-bold">Lunar</span>
                    :{{item.info.miss_distance.lunar}}
                  </li>
                  <li>
                    <span class="font-weight-bold">Quilômetros</span>
                    :{{item.info.miss_distance.kilometers}}
                  </li>
                  <li>
                    <span class="font-weight-bold">Milhas</span>
                    :{{item.info.miss_distance.miles}}
                  </li>
                </ul>
              </li>

              <li>
                <span class="font-weight-black">Diâmetro</span>:
                <ul>
                  <li>
                    <span class="font-weight-bold">Quilômetros</span>:
                    <ul>
                      <li>
                        <span class="font-weight-medium">Min</span>
                        :{{item.diameter.kilometers.estimated_diameter_min}}
                      </li>
                      <li>
                        <span class="font-weight-medium">Max</span>
                        :{{item.diameter.kilometers.estimated_diameter_max}}
                      </li>
                    </ul>
                  </li>
                  <li>
                    <span class="font-weight-bold">Metros</span>:
                    <ul>
                      <li>
                        <span class="font-weight-medium">Min</span>
                        :{{item.diameter.meters.estimated_diameter_min}}
                      </li>
                      <li>
                        <span class="font-weight-medium">Max</span>
                        :{{item.diameter.meters.estimated_diameter_max}}
                      </li>
                    </ul>
                  </li>
                  <li>
                    <span class="font-weight-bold">Milhas</span>:
                    <ul>
                      <li>
                        <span class="font-weight-medium">Min</span>
                        :{{item.diameter.miles.estimated_diameter_min}}
                      </li>
                      <li>
                        <span class="font-weight-medium">Max</span>
                        :{{item.diameter.miles.estimated_diameter_max}}
                      </li>
                    </ul>
                  </li>
                  <li>
                    <span class="font-weight-bold">Pés</span>:
                    <ul>
                      <li>
                        <span class="font-weight-medium">Min</span>
                        :{{item.diameter.feet.estimated_diameter_min}}
                      </li>
                      <li>
                        <span class="font-weight-medium">Max</span>
                        :{{item.diameter.feet.estimated_diameter_max}}
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
        <v-card-actions class="d-flex justify-space-between">
          <v-btn @click="prevPage()" color="orange" :disabled="page == 1" text>Anterior</v-btn>
          <div>
            <span>{{page}}/{{infoMeteor.totalPages}}</span>
          </div>
          <v-btn
            @click="nextPage()"
            color="orange"
            :disabled="page == infoMeteor.totalPages"
            text
          >Próximo</v-btn>
        </v-card-actions>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import api from "../../services/api";
export default {
  data() {
    return {
      meteor: [],
      infoMeteor: {
        totalPages: ""
      },
      page: 1,
      startDate: "",
      endDate: "",
      treeView: [],
      search: "",
      loading: true,
      menu1: false,
      menu2: false,
      dialog:false,
      message:'',
    };
  },
  mounted() {
      this.initDate();
    this.getMetoers();
    
  },
  methods: {
    getMetoers(page = 1) {
    
      if (this.validateDate()) {
        this.loading = true;
        api
          .get(
            `/meteor?page=${page}&startDate=${this.startDate}&endDate=${this.endDate}`
          )
          .then(response => {
            this.meteor = response.data.data;
            this.infoMeteor.totalPages = response.data.totalPage;
            this.page = response.data.currentPage;
          })
          .finally(() => (this.loading = false));
      }
      else
      {
          this.dialog = true;
          this.message = "Informe datas validas, com no máximo 7 dias de diferença";
          
      }
    },
    validateDate()
    {
        let startDate = new Date(this.startDate);
        let endDate = new Date(this.endDate)
        

        if(startDate.getTime() > endDate.getTime())
        {
            return false;
        }
        let diffDate = (endDate.getTime() - startDate.getTime())  / (1000 * 3600 * 24);
        
        if(diffDate > 7)
        {
            return false;
        }

        return true;
    },
    nextPage() {
      if (this.page == this.infoMeteor.totalPages) return;

      const pageNumber = this.page + 1;

      this.getMetoers(pageNumber);
    },
    prevPage() {
      if (this.page === 1) return;

      const pageNumber = this.page - 1;

      this.getMetoers(pageNumber);
    },

    findByName(evt) {
      if (this.search) {
        evt = evt ? evt : window.event;
        let charCode = evt.which ? evt.which : evt.keyCode;
        if (
          (charCode >= 48 && charCode <= 57) ||
          (charCode >= 65 && charCode <= 90) ||
          (charCode >= 97 && charCode <= 105) ||
          charCode === 8 ||
          charCode === 13 ||
          charCode === 32
        ) {
          this.loading = true;
          api
            .get(`/meteor?page=${this.page}&search=${this.search}`)
            .then(response => {
              this.meteor = response.data.data;
              this.infoMeteor.pages = response.data.totalPage;
              this.page = response.data.currentPage;
            })
            .finally(() => (this.loading = false));
        } else {
          evt.preventDefault();
        }
      }
    },
    initDate() {
      let currentDate = new Date();
      this.startDate = currentDate.toISOString().substr(0, 10);
      currentDate.setDate(currentDate.getDate() + 7);
      this.endDate = currentDate.toISOString().substr(0, 10);
    }
  }
};
</script>

<style scoped>
#main {
  margin-top: 20px;
}
</style>