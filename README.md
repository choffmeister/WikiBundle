WikiBundle
==========

What is WikiBundle?
-------------------

This is a bundle for Symfony2. It provides a parser and two
renders for Wiki markup ([Creole v1.0][1]). The parser and
the lexer transfer the markup into an class oriented tree
structure. This structure is used by the `XhtmlRenderer`
and `LatexRenderer` to create an formatted output. Its
easy to write your very own renderer.

Performance
-----------

The parsing is quiet fast (benchmarks will follow). Additional
its is easy to cache the parsed result. Each class representing
the tree structure implements the `Serializable` interface. So
each time the markup changes, you can parse it once and then store
the serialized form of the tree (for example with Doctrine 2 in
a column of type `Object`). Once you need to render the markup
you can just deserialize the tree (which is way faster) and then
render it directly without a need to reparse it.

To do
-----

Following markup is missing by now:

* [Images][2]: Will follow soon
* [Placeholder][3]: Not sure if I want to implement it

[1]: http://www.wikicreole.org/wiki/Creole1.0
[2]: http://www.wikicreole.org/wiki/Creole1.0#section-Creole1.0-ImageInline
[3]: http://www.wikicreole.org/wiki/Creole1.0#section-Creole1.0-Placeholder
