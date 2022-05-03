import { NgModule, LOCALE_ID } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ValidarComponent } from './validar/validar.component';
import { ClarityModule } from '@clr/angular';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { registerLocaleData } from '@angular/common';
import localeCo from '@angular/common/locales/es-CO';
import { FileSaverModule } from 'ngx-filesaver';
import { ConsultarComponent } from './consultar/consultar.component';
import { LoginComponent } from './consultar/login/login.component';
import { DescargarComponent } from './consultar/descargar/descargar.component';
import { GenerarComponent } from './consultar/generar/generar.component';

registerLocaleData(localeCo);

@NgModule({
  declarations: [
    AppComponent,
    ValidarComponent,
    ConsultarComponent,
    LoginComponent,
    DescargarComponent,
    GenerarComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    AppRoutingModule,
    ClarityModule,
    BrowserAnimationsModule,
    FileSaverModule
  ],
  providers: [{ provide: LOCALE_ID, useValue: 'es-CO' }],
  bootstrap: [AppComponent]
})
export class AppModule { }
