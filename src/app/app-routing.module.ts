import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { ValidarComponent } from './validar/validar.component';
import { ConsultarComponent } from './consultar/consultar.component';
import { DescargarComponent } from './consultar/descargar/descargar.component';
import { GenerarComponent } from './consultar/generar/generar.component';
import { UtilidadesComponent } from './consultar/utilidades/utilidades.component';
import { NotificarComponent } from './consultar/utilidades/notificar/notificar.component';
import { GeneradorComponent } from './consultar/utilidades/generador/generador.component';

const routes: Routes = [
  { path: '', redirectTo: 'validar', pathMatch: 'full' },
  { path: 'validar', component: ValidarComponent },
  {
    path: 'consultar', component: ConsultarComponent, children: [
      { path: '', redirectTo: 'descargar', pathMatch: 'full' },
      { path: 'descargar', component: DescargarComponent },
      { path: 'generar', component: GenerarComponent },
      {
        path: 'utilidades', component: UtilidadesComponent, children: [
          { path: '', redirectTo: 'notificar', pathMatch: 'full' },
          { path: 'notificar', component: NotificarComponent },
          { path: 'generador', component: GeneradorComponent }
        ]
      },
    ]
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
