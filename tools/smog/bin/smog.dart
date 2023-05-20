import 'dart:async';
import 'package:http/http.dart' as http;
import 'package:args/args.dart';

void main(List<String> arguments) async 
{

  ArgParser parser = ArgParser()
    ..addOption('load', abbr: 'l')
    ..addOption('endpoint', abbr: 'e')
    ..addFlag('silent', negatable: false, abbr: 's');
  ArgResults results = parser.parse(arguments);

  final String url = results['endpoint'];
  final int load = int.parse(results['load']);

  final List<Future<http.Response>> futures = List.generate(load, (index) => getRequest(url));
  await Future.wait(futures, eagerError: true);

}

Future<http.Response> getRequest(String url) async 
{
  final response = await http.get(Uri.parse(url));
  return response;
}