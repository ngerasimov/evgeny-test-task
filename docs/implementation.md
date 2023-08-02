# Data structures

**Module**
- name
- code

**MeasurementType**
- name
- code
- units

Each Module must have at least one MeasurementType to generate values in simulator. Module-MeasurementType binding is implemented through many-to-many relation between these entities.

Btw a Module can have two or more measurements.

**State**
- name
- code
- is_operable

`is_operable` attribute controls value generation: when a module is in state with `is_operable=0` then no values are generated.

Actually there are only two states: 
- `operate` (`is_operable=1`) and 
- `breakdown` (`is_operable=0`).

(Theoretically there is a possibility to have for each module number of states larger than only on/off).

**MeasuredValues**
- module
- measurementType
- value
- datetime

Generated measurement values as is. Source data for the stats and charts.

**StateHistory**
- module
- state
- datetime

On/Off switching sequences for eah module. Source data for the stats and charts. 

# Simulation module
## Architecture

Consist of two services: Generator and Persister. Generator creates measurement values according to Module/Measurements structure and puts it into RabbitMQ queue. Persister takes values from that queue and saves it into DB. 

Main implementation class of the Generator is `App\Simulation\MeasurementGenerator`. It runs through the Symfony console command `app:simulate` (`App\Command\SimulateCommand`)  

Persister is implemented in message handlers classes under the `App\MessageHandler` namespace. It runs from separate restartable worker process in `app_worker_webreathe` container. Because of Doctrine's memory leaks there are often out-of-memory faults happen after which Persister process simple restarts without any impact on the simulation process in the Generator.

Simulation can work in two modes: realtime and 
virtual (no-realtime). In realtime mode there is a full mimic of some device that sends values. This mode is default. Simulation can be started and new generated values can be observed on Website simultaneously. 

In virtual mode bulk generating of values set for the given time period is performed without reference to the real time. Simulation time start from the past to prevent generation of the 'future' event/state values. 

## Algorithm 
Implemented in `App\Simulation\Model\Measure` and `App\Simulation\Model\Module`

To create not much dispersed value series it applies some kind of smoothing. First, the time line is divided on 'stages'. Length of the stage in seconds is set by `MEASURE_DEFAULT_STAGE_DURATION` parameter and it is larger than measuremet-value generation time interval. For each new stage time point a new reference-value is generated randomly in range given by `MEASURE_DEFAULT_VALUE_MIN` and `MEASURE_DEFAULT_VALUE_MAX` parameters.

Inside each stage interval there are few 'ticks' - intervals of the actual measurement value generation. Values on each tick change linearly from value being set at the begin of the stage to the one at the end of the stage interval. Tick length is set in 'MEASURE_DEFAULT_TICK_SEC' parameter.

In parallel of measurement values generation there are state changing events are generated. This process is controlled by `MODULE_DEFAULT_BREAKDOWN_PERIOD` (average time between breakdowns) and `MODULE_DEFAULT_BREAKDOWN_DURATION` parameters.

All of these parameters can be set through `.env` file in the project root.


# Website
## Main page
Shows table with stats for every module in the system. Displayed parameters are:
- current module state
- module working time: total and specifically in each state
- measurement stats: number of records, time of the first measurement, time of the last measurement (n.b. in general time between first and last measurement is not equal to module working total time)

Click on Measurement button leads to Chart page.

## Chart page
Displays interpolated chart visualization of values' and states' dynamics for the given module, measuremet type and time period. Amount of data being displayed is limited by number of seconds given in `CHART_TIME_LENGTH` parameter. Time between interpolated chart points in seconds is set by `CHART_TIME_STEP` parameter. 