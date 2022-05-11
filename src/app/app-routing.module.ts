import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { ValidarComponent } from './validar/validar.component';
import { ConsultarComponent } from './consultar/consultar.component';
import { DescargarComponent } from './consultar/descargar/descargar.component';
import { GenerarComponent } from './consultar/generar/generar.component';
import { NotificarComponent } from './consultar/notificar/notificar.component';

const routes: Routes = [
  { path: '', redirectTo: 'validar', pathMatch: 'full' },
  { path: 'validar', component: ValidarComponent },
  {
    path: 'consultar', component: ConsultarComponent, children: [
      { path: 'descargar', component: DescargarComponent },
      { path: 'generar', component: GenerarComponent },
      { path: 'notificar', component: NotificarComponent },
    ]
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
