# Plugin Swimo

# Description

Este plug-in permite conectar controladores de pool Swimo com Jeedom.
O plugin torna possível recuperar os valores dos vários sensores, bem como controlar os atuadores (mudança de modos, On / Off, mudança de setpoints).

# Configuração do plugin

Depois de baixar o plugin, você só precisa ativá-lo e configurar alguns elementos :

- o tipo de conexão *(local ou nuvem)*.
- o endereço IP swimo.
- swimo serial.
- O apikey de swimo.

![swimo](../images/swimo1.png)

# Configuração do equipamento

A sincronização do equipamento permite recuperar as sondas e atuadores configurados no Swimo.

![swimo2](../images/swimo2.png)

Depois de configurado em um objeto, você receberá seus elementos no painel :

![swimo3](../images/swimo3.png)

> **NOTA**
>
> - Os dados são atualizados a cada 5 minutos ou durante uma ação.
> - Os pontos de ajuste não são exibidos por padrão.
> Eles são totalmente utilizáveis através dos cenários.
